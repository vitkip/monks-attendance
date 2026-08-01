<?php

namespace App\Http\Controllers;

use App\Models\DutySchedule;
use App\Models\Monk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class DutyScheduleController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');
        $filterType = (string) $request->query('type', '');

        $applySearch = fn ($q) => $q->whereHas('monk', fn ($mq) => $mq->where('name', 'like', "%{$search}%")
            ->orWhere('surname', 'like', "%{$search}%")
        )->orWhere('duty_name', 'like', "%{$search}%");

        $mapDuty = fn ($duty) => [
            'id' => $duty->id,
            'monk_id' => $duty->monk_id,
            'duty_name' => $duty->duty_name,
            'description' => $duty->description,
            'schedule_type' => $duty->schedule_type,
            'duty_date' => $duty->duty_date?->format('Y-m-d'),
            'day_of_week' => $duty->day_of_week,
            'monk' => [
                'full_name' => $duty->monk->full_name,
                'type_label' => $duty->monk->type_label,
                'photo_url' => $duty->monk->photo_url,
            ],
        ];

        $weeklyGroups = $filterType !== 'once'
            ? DutySchedule::with('monk')->where('schedule_type', 'weekly')
                ->when($search, $applySearch)
                ->orderBy('day_of_week')
                ->get()
                ->groupBy('day_of_week')
                ->map(fn ($duties, $day) => [
                    'day' => (int) $day,
                    'day_name' => DutySchedule::$dayNames[$day] ?? '',
                    'duties' => $duties->values()->map($mapDuty),
                ])->values()
            : collect();

        $onceGroups = $filterType !== 'weekly'
            ? DutySchedule::with('monk')->where('schedule_type', 'once')
                ->when($search, $applySearch)
                ->orderBy('duty_date')
                ->get()
                ->groupBy(fn ($d) => $d->duty_date->format('Y-m-d'))
                ->map(fn ($duties, $date) => [
                    'date' => $date,
                    'duties' => $duties->values()->map($mapDuty),
                ])->values()
            : collect();

        $monks = Monk::where('status', 'active')->orderBy('name')->get();

        return Inertia::render('DutySchedules/Index', [
            'filters' => ['search' => $search, 'type' => $filterType],
            'weeklyGroups' => $weeklyGroups,
            'onceGroups' => $onceGroups,
            'monks' => $monks->map(fn ($m) => ['id' => $m->id, 'full_name' => $m->full_name, 'type_label' => $m->type_label]),
            'totalWeekly' => DutySchedule::where('schedule_type', 'weekly')->count(),
            'totalOnce' => DutySchedule::where('schedule_type', 'once')->count(),
            'dayNames' => DutySchedule::$dayNames,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateDuty($request);
        DutySchedule::create($data);

        return back()->with('success', 'ບັນທຶກໜ້າທີ່ສຳເລັດ');
    }

    public function update(Request $request, DutySchedule $dutySchedule): RedirectResponse
    {
        $data = $this->validateDuty($request);
        $dutySchedule->update($data);

        return back()->with('success', 'ແກ້ໄຂຂໍ້ມູນໜ້າທີ່ສຳເລັດ');
    }

    public function destroy(DutySchedule $dutySchedule): RedirectResponse
    {
        $dutySchedule->delete();

        return back()->with('success', 'ລຶບຂໍ້ມູນໜ້າທີ່ສຳເລັດ');
    }

    private function validateDuty(Request $request): array
    {
        $scheduleType = $request->input('schedule_type');

        $rules = [
            'monk_id' => 'required|exists:monks,id',
            'schedule_type' => ['required', Rule::in(['once', 'weekly'])],
            'duty_name' => 'required|string|max:150',
            'description' => 'nullable|string|max:500',
        ];

        $rules += $scheduleType === 'once'
            ? ['duty_date' => 'required|date']
            : ['day_of_week' => 'required|integer|between:1,7'];

        $validated = Validator::make($request->all(), $rules, [
            'monk_id.required' => 'ກະລຸນາເລືອກພຣະສົງ/ສາມະເນນ',
            'duty_name.required' => 'ກະລຸນາລະບຸຊື່ໜ້າທີ່',
            'duty_date.required' => 'ກະລຸນາເລືອກວັນທີ',
            'day_of_week.required' => 'ກະລຸນາເລືອກວັນໃນອາທິດ',
            'day_of_week.between' => 'ວັນໃນອາທິດຕ້ອງຢູ່ລະຫວ່າງ 1-7',
        ])->validate();

        return [
            'monk_id' => $validated['monk_id'],
            'schedule_type' => $validated['schedule_type'],
            'duty_name' => $validated['duty_name'],
            'description' => $validated['description'] ?: null,
            'duty_date' => $scheduleType === 'once' ? $validated['duty_date'] : null,
            'day_of_week' => $scheduleType === 'weekly' ? $validated['day_of_week'] : null,
        ];
    }
}
