<?php

namespace App\Livewire\DutySchedules;

use Livewire\Component;
use App\Models\DutySchedule;
use App\Models\Monk;

class DutyScheduleIndex extends Component
{
    public string $search = '';
    public string $filterType = '';
    public bool $showModal = false;
    public ?int $editId = null;

    public int|string $monk_id = '';
    public string $schedule_type = 'once';
    public string $duty_name = '';
    public string $duty_date = '';
    public int|string $day_of_week = '';
    public string $description = '';

    public function rules(): array
    {
        $dateRules = $this->schedule_type === 'once'
            ? ['duty_date' => 'required|date']
            : ['day_of_week' => 'required|integer|between:1,7'];

        return array_merge([
            'monk_id'       => 'required|exists:monks,id',
            'schedule_type' => 'required|in:once,weekly',
            'duty_name'     => 'required|string|max:150',
            'description'   => 'nullable|string|max:500',
        ], $dateRules);
    }

    protected $messages = [
        'monk_id.required'       => 'ກະລຸນາເລືອກພຣະສົງ/ສາມະເນນ',
        'duty_name.required'     => 'ກະລຸນາລະບຸຊື່ໜ້າທີ່',
        'duty_date.required'     => 'ກະລຸນາເລືອກວັນທີ',
        'day_of_week.required'   => 'ກະລຸນາເລືອກວັນໃນອາທິດ',
        'day_of_week.between'    => 'ວັນໃນອາທິດຕ້ອງຢູ່ລະຫວ່າງ 1-7',
    ];

    public function updatedScheduleType(): void
    {
        $this->duty_date  = '';
        $this->day_of_week = '';
        $this->resetValidation();
    }

    public function openCreate(): void
    {
        $this->reset(['monk_id', 'duty_name', 'duty_date', 'day_of_week', 'description', 'editId']);
        $this->schedule_type = 'once';
        $this->duty_date     = now()->format('Y-m-d');
        $this->showModal     = true;
    }

    public function openEdit(int $id): void
    {
        $duty = DutySchedule::findOrFail($id);
        $this->editId        = $id;
        $this->monk_id       = $duty->monk_id;
        $this->schedule_type = $duty->schedule_type;
        $this->duty_name     = $duty->duty_name;
        $this->duty_date     = $duty->duty_date?->format('Y-m-d') ?? '';
        $this->day_of_week   = $duty->day_of_week ?? '';
        $this->description   = $duty->description ?? '';
        $this->showModal     = true;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'monk_id'       => $this->monk_id,
            'schedule_type' => $this->schedule_type,
            'duty_name'     => $this->duty_name,
            'description'   => $this->description ?: null,
            'duty_date'     => $this->schedule_type === 'once' ? $this->duty_date : null,
            'day_of_week'   => $this->schedule_type === 'weekly' ? $this->day_of_week : null,
        ];

        if ($this->editId) {
            DutySchedule::findOrFail($this->editId)->update($data);
            session()->flash('success', 'ແກ້ໄຂຂໍ້ມູນໜ້າທີ່ສຳເລັດ');
        } else {
            DutySchedule::create($data);
            session()->flash('success', 'ບັນທຶກໜ້າທີ່ສຳເລັດ');
        }

        $this->showModal = false;
        $this->reset(['monk_id', 'schedule_type', 'duty_name', 'duty_date', 'day_of_week', 'description', 'editId']);
        $this->schedule_type = 'once';
    }

    public function delete(int $id): void
    {
        DutySchedule::findOrFail($id)->delete();
        session()->flash('success', 'ລຶບຂໍ້ມູນໜ້າທີ່ສຳເລັດ');
    }

    public function render()
    {
        $applySearch = fn($q) =>
            $q->whereHas('monk', fn($mq) =>
                $mq->where('name', 'like', "%{$this->search}%")
                   ->orWhere('surname', 'like', "%{$this->search}%")
            )->orWhere('duty_name', 'like', "%{$this->search}%");

        $weeklyGroups = ($this->filterType !== 'once')
            ? DutySchedule::with('monk')
                ->where('schedule_type', 'weekly')
                ->when($this->search, $applySearch)
                ->orderBy('day_of_week')
                ->get()
                ->groupBy('day_of_week')
            : collect();

        $onceGroups = ($this->filterType !== 'weekly')
            ? DutySchedule::with('monk')
                ->where('schedule_type', 'once')
                ->when($this->search, $applySearch)
                ->orderBy('duty_date')
                ->get()
                ->groupBy(fn($d) => $d->duty_date->format('Y-m-d'))
            : collect();

        $monks = Monk::where('status', 1)->orderBy('name')->get();

        $totalWeekly = DutySchedule::where('schedule_type', 'weekly')->count();
        $totalOnce   = DutySchedule::where('schedule_type', 'once')->count();

        $dayNames = DutySchedule::$dayNames;

        return view('livewire.duty-schedules.duty-schedule-index',
            compact('weeklyGroups', 'onceGroups', 'monks', 'totalWeekly', 'totalOnce', 'dayNames'));
    }
}
