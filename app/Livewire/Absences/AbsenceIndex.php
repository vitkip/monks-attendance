<?php

namespace App\Livewire\Absences;

use Livewire\Component;
use App\Models\Absence;
use App\Models\Monk;
use App\Models\FineRate;
use App\Models\DutySchedule;

class AbsenceIndex extends Component
{
    public string $search = '';
    public string $filterMonth = '';
    public string $filterPaid = '';
    public bool $showModal = false;
    public ?int $editId = null;

    public int|string $monk_id = '';
    public array $monk_ids = [];
    public int|string $fine_rate_id = '';
    public string $absent_date = '';
    public string $reason = '';
    public float|string $fine_amount = 0;
    public int $is_paid = 0;
    public string $note = '';

    protected function rules(): array
    {
        $monkRule = $this->editId
            ? ['monk_id' => 'required|exists:monks,id']
            : ['monk_ids' => 'required|array|min:1', 'monk_ids.*' => 'exists:monks,id'];

        return array_merge($monkRule, [
            'fine_rate_id' => 'required|exists:fine_rates,id',
            'absent_date'  => 'required|date',
            'reason'       => 'nullable|string|max:255',
            'fine_amount'  => 'required|numeric|min:0',
            'is_paid'      => 'boolean',
            'note'         => 'nullable|string|max:500',
        ]);
    }

    protected $messages = [
        'monk_id.required'      => 'ກະລຸນາເລືອກພຣະສົງ/ສາມະເນນ',
        'monk_ids.required'     => 'ກະລຸນາເລືອກພຣະສົງ/ສາມະເນນຢ່າງໜ້ອຍໜຶ່ງອົງ',
        'fine_rate_id.required' => 'ກະລຸນາເລືອກປະເພດຄ່າປັບ',
        'absent_date.required'  => 'ກະລຸນາເລືອກວັນທີ',
        'fine_amount.required'  => 'ກະລຸນາປ້ອນຈຳນວນເງິນ',
    ];

    public function updatedFineRateId($value): void
    {
        if ($value) {
            $rate = FineRate::find($value);
            if ($rate) {
                $this->fine_amount = $rate->amount;
            }
        }
    }

    public function openCreate(): void
    {
        $this->reset(['monk_id', 'monk_ids', 'fine_rate_id', 'absent_date', 'reason', 'fine_amount', 'is_paid', 'note', 'editId']);
        $this->absent_date = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $absence = Absence::findOrFail($id);
        $this->editId       = $id;
        $this->monk_id      = $absence->monk_id;
        $this->monk_ids     = [];
        $this->fine_rate_id = $absence->fine_rate_id;
        $this->absent_date  = $absence->absent_date->format('Y-m-d');
        $this->reason       = $absence->reason ?? '';
        $this->fine_amount  = $absence->fine_amount;
        $this->is_paid      = $absence->is_paid;
        $this->note         = $absence->note ?? '';
        $this->showModal    = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'fine_rate_id' => $this->fine_rate_id,
            'absent_date'  => $this->absent_date,
            'reason'       => $this->reason ?: null,
            'fine_amount'  => $this->fine_amount,
            'is_paid'      => $this->is_paid,
            'note'         => $this->note ?: null,
        ];

        if ($this->editId) {
            $conflict = $this->dutyConflictMessage((int) $this->monk_id);
            if ($conflict) {
                $this->addError('absent_date', $conflict);
                return;
            }

            Absence::findOrFail($this->editId)->update($data + ['monk_id' => $this->monk_id]);
            session()->flash('success', 'ແກ້ໄຂຂໍ້ມູນສຳເລັດ');
        } else {
            $monkIds = array_unique($this->monk_ids);

            $conflicts = array_filter(array_map(
                fn ($monkId) => $this->dutyConflictMessage((int) $monkId),
                $monkIds
            ));

            if (!empty($conflicts)) {
                $this->addError('absent_date', implode(' | ', $conflicts));
                return;
            }

            foreach ($monkIds as $monkId) {
                Absence::create($data + ['monk_id' => $monkId]);
            }
            session()->flash('success', 'ບັນທຶກການຂາດລາສຳເລັດ (' . count($monkIds) . ' ອົງ)');
        }

        $this->showModal = false;
        $this->reset(['monk_id', 'monk_ids', 'fine_rate_id', 'absent_date', 'reason', 'fine_amount', 'is_paid', 'note', 'editId']);
    }

    protected function dutyConflictMessage(int $monkId): ?string
    {
        $duty = DutySchedule::monkHasDutyOn($monkId, $this->absent_date);

        if (!$duty) {
            return null;
        }

        $monk = Monk::find($monkId);
        $label = $duty->schedule_type === 'weekly'
            ? '(ໝຸນວຽນ ' . $duty->day_name . ')'
            : '(ວັນທີ ' . \Carbon\Carbon::parse($this->absent_date)->format('d/m/Y') . ')';

        return 'ບໍ່ສາມາດໝາຍຂາດໄດ້ — ' . ($monk?->full_name ?? '') .
            ' ມີໜ້າທີ່ "' . $duty->duty_name . '" ' . $label;
    }

    public function markPaid(int $id): void
    {
        Absence::findOrFail($id)->update(['is_paid' => 1]);
        session()->flash('success', 'ອັບເດດສະຖານະສຳເລັດ');
    }

    public function delete(int $id): void
    {
        Absence::findOrFail($id)->delete();
        session()->flash('success', 'ລຶບຂໍ້ມູນສຳເລັດ');
    }

    public function render()
    {
        $absenceGroups = Absence::with(['monk', 'fineRate'])
            ->when($this->search, fn($q) =>
                $q->whereHas('monk', fn($mq) =>
                    $mq->where('name', 'like', "%{$this->search}%")
                       ->orWhere('surname', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterMonth, fn($q) =>
                $q->whereYear('absent_date', substr($this->filterMonth, 0, 4))
                  ->whereMonth('absent_date', substr($this->filterMonth, 5, 2))
            )
            ->when($this->filterPaid !== '', fn($q) =>
                $q->where('is_paid', $this->filterPaid)
            )
            ->latest('absent_date')
            ->get()
            ->groupBy(fn ($absence) => $absence->absent_date->format('Y-m-d'));

        $monks     = Monk::where('status', 1)->orderBy('name')->get();
        $fineRates = FineRate::orderBy('name')->get();

        $dutySchedules = DutySchedule::with('monk')
            ->whereIn('monk_id', $monks->pluck('id'))
            ->get();

        $onceMonkGroups = $dutySchedules->where('schedule_type', 'once')
            ->sortBy('duty_date')
            ->groupBy(fn ($d) => $d->duty_date->format('Y-m-d'));

        $weeklyMonkGroups = $dutySchedules->where('schedule_type', 'weekly')
            ->sortBy('day_of_week')
            ->groupBy('day_of_week');

        $monksWithoutDuty = $monks->whereNotIn('id', $dutySchedules->pluck('monk_id')->unique())->values();

        $dayNames = DutySchedule::$dayNames;

        $totalFine  = Absence::when($this->filterMonth, fn($q) =>
            $q->whereYear('absent_date', substr($this->filterMonth, 0, 4))
              ->whereMonth('absent_date', substr($this->filterMonth, 5, 2))
        )->sum('fine_amount');

        $unpaidFine = Absence::where('is_paid', 0)->sum('fine_amount');

        return view('livewire.absences.absence-index', compact(
            'absenceGroups', 'monks', 'fineRates', 'totalFine', 'unpaidFine',
            'onceMonkGroups', 'weeklyMonkGroups', 'monksWithoutDuty', 'dayNames'
        ));
    }
}
