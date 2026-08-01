<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\DutySchedule;
use App\Models\FineRate;
use App\Models\Monk;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AbsenceController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');
        $filterMonth = (string) $request->query('month', '');
        $filterPaid = (string) $request->query('paid', '');

        $absenceGroups = Absence::with(['monk', 'fineRate'])
            ->when($search, fn ($q) => $q->whereHas('monk', fn ($mq) => $mq->where('name', 'like', "%{$search}%")
                ->orWhere('surname', 'like', "%{$search}%")
            ))
            ->when($filterMonth, fn ($q) => $q->whereYear('absent_date', substr($filterMonth, 0, 4))
                ->whereMonth('absent_date', substr($filterMonth, 5, 2))
            )
            ->when($filterPaid !== '', fn ($q) => $q->where('is_paid', $filterPaid))
            ->latest('absent_date')
            ->get()
            ->groupBy(fn ($absence) => $absence->absent_date->format('Y-m-d'))
            ->map(fn ($group, $dateKey) => [
                'date' => $dateKey,
                'total' => $group->sum('fine_amount'),
                'items' => $group->values()->map(fn ($absence) => [
                    'id' => $absence->id,
                    'monk' => [
                        'id' => $absence->monk->id,
                        'full_name' => $absence->monk->full_name,
                        'type' => $absence->monk->type,
                        'type_label' => $absence->monk->type_label,
                    ],
                    'fine_rate' => ['id' => $absence->fine_rate_id, 'name' => $absence->fineRate->name],
                    'reason' => $absence->reason,
                    'fine_amount' => (float) $absence->fine_amount,
                    'is_paid' => (bool) $absence->is_paid,
                    'note' => $absence->note,
                ]),
            ])
            ->values();

        $monks = Monk::where('status', 1)->orderBy('name')->get();
        $fineRates = FineRate::orderBy('name')->get(['id', 'name', 'amount']);

        $dutySchedules = DutySchedule::with('monk')
            ->whereIn('monk_id', $monks->pluck('id'))
            ->get();

        $onceMonkGroups = $dutySchedules->where('schedule_type', 'once')
            ->sortBy('duty_date')
            ->groupBy(fn ($d) => $d->duty_date->format('Y-m-d'))
            ->map(fn ($duties, $date) => [
                'date' => $date,
                'duties' => $duties->values()->map(fn ($d) => [
                    'id' => $d->id,
                    'monk_id' => $d->monk_id,
                    'monk_name' => $d->monk->full_name,
                    'duty_name' => $d->duty_name,
                ]),
            ])->values();

        $weeklyMonkGroups = $dutySchedules->where('schedule_type', 'weekly')
            ->sortBy('day_of_week')
            ->groupBy('day_of_week')
            ->map(fn ($duties, $day) => [
                'day' => (int) $day,
                'day_name' => DutySchedule::$dayNames[$day] ?? '',
                'duties' => $duties->values()->map(fn ($d) => [
                    'id' => $d->id,
                    'monk_id' => $d->monk_id,
                    'monk_name' => $d->monk->full_name,
                    'duty_name' => $d->duty_name,
                ]),
            ])->values();

        $monksWithoutDuty = $monks->whereNotIn('id', $dutySchedules->pluck('monk_id')->unique())->values()
            ->map(fn ($m) => ['id' => $m->id, 'full_name' => $m->full_name, 'type_label' => $m->type_label]);

        $totalFine = Absence::when($filterMonth, fn ($q) => $q->whereYear('absent_date', substr($filterMonth, 0, 4))
            ->whereMonth('absent_date', substr($filterMonth, 5, 2))
        )->sum('fine_amount');

        $unpaidFine = Absence::where('is_paid', 0)->sum('fine_amount');

        return Inertia::render('Absences/Index', [
            'filters' => ['search' => $search, 'month' => $filterMonth, 'paid' => $filterPaid],
            'absenceGroups' => $absenceGroups,
            'monks' => $monks->map(fn ($m) => ['id' => $m->id, 'full_name' => $m->full_name, 'type_label' => $m->type_label]),
            'fineRates' => $fineRates,
            'onceMonkGroups' => $onceMonkGroups,
            'weeklyMonkGroups' => $weeklyMonkGroups,
            'monksWithoutDuty' => $monksWithoutDuty,
            'totalFine' => (float) $totalFine,
            'unpaidFine' => (float) $unpaidFine,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateAbsence($request, isEdit: false);

        $monkIds = array_unique($data['monk_ids']);

        $conflicts = array_filter(array_map(
            fn ($monkId) => $this->dutyConflictMessage((int) $monkId, $data['absent_date']),
            $monkIds
        ));

        if (! empty($conflicts)) {
            throw ValidationException::withMessages(['absent_date' => implode(' | ', $conflicts)]);
        }

        foreach ($monkIds as $monkId) {
            Absence::create([
                'monk_id' => $monkId,
                'fine_rate_id' => $data['fine_rate_id'],
                'absent_date' => $data['absent_date'],
                'reason' => $data['reason'] ?: null,
                'fine_amount' => $data['fine_amount'],
                'is_paid' => $data['is_paid'],
                'note' => $data['note'] ?: null,
            ]);
        }

        return back()->with('success', 'ບັນທຶກການຂາດລາສຳເລັດ (' . count($monkIds) . ' ອົງ)');
    }

    public function update(Request $request, Absence $absence): RedirectResponse
    {
        $data = $this->validateAbsence($request, isEdit: true);

        $conflict = $this->dutyConflictMessage((int) $data['monk_id'], $data['absent_date']);
        if ($conflict) {
            throw ValidationException::withMessages(['absent_date' => $conflict]);
        }

        $absence->update([
            'monk_id' => $data['monk_id'],
            'fine_rate_id' => $data['fine_rate_id'],
            'absent_date' => $data['absent_date'],
            'reason' => $data['reason'] ?: null,
            'fine_amount' => $data['fine_amount'],
            'is_paid' => $data['is_paid'],
            'note' => $data['note'] ?: null,
        ]);

        return back()->with('success', 'ແກ້ໄຂຂໍ້ມູນສຳເລັດ');
    }

    public function markPaid(Absence $absence): RedirectResponse
    {
        $absence->update(['is_paid' => 1]);

        return back()->with('success', 'ອັບເດດສະຖານະສຳເລັດ');
    }

    public function destroy(Absence $absence): RedirectResponse
    {
        $absence->delete();

        return back()->with('success', 'ລຶບຂໍ້ມູນສຳເລັດ');
    }

    private function validateAbsence(Request $request, bool $isEdit): array
    {
        $rules = [
            'fine_rate_id' => 'required|exists:fine_rates,id',
            'absent_date' => 'required|date',
            'reason' => 'nullable|string|max:255',
            'fine_amount' => 'required|numeric|min:0',
            'is_paid' => 'boolean',
            'note' => 'nullable|string|max:500',
        ];

        $rules += $isEdit
            ? ['monk_id' => 'required|exists:monks,id']
            : ['monk_ids' => 'required|array|min:1', 'monk_ids.*' => 'exists:monks,id'];

        $messages = [
            'monk_id.required' => 'ກະລຸນາເລືອກພຣະສົງ/ສາມະເນນ',
            'monk_ids.required' => 'ກະລຸນາເລືອກພຣະສົງ/ສາມະເນນຢ່າງໜ້ອຍໜຶ່ງອົງ',
            'fine_rate_id.required' => 'ກະລຸນາເລືອກປະເພດຄ່າປັບ',
            'absent_date.required' => 'ກະລຸນາເລືອກວັນທີ',
            'fine_amount.required' => 'ກະລຸນາປ້ອນຈຳນວນເງິນ',
        ];

        return Validator::make($request->all(), $rules, $messages)->validate();
    }

    private function dutyConflictMessage(int $monkId, string $absentDate): ?string
    {
        $duty = DutySchedule::monkHasDutyOn($monkId, $absentDate);

        if (! $duty) {
            return null;
        }

        $monk = Monk::find($monkId);
        $label = $duty->schedule_type === 'weekly'
            ? '(ໝຸນວຽນ ' . $duty->day_name . ')'
            : '(ວັນທີ ' . Carbon::parse($absentDate)->format('d/m/Y') . ')';

        return 'ບໍ່ສາມາດໝາຍຂາດໄດ້ — ' . ($monk?->full_name ?? '') .
            ' ມີໜ້າທີ່ "' . $duty->duty_name . '" ' . $label;
    }
}
