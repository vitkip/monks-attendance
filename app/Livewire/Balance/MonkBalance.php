<?php

namespace App\Livewire\Balance;

use Livewire\Component;
use App\Models\Monk;
use App\Models\Absence;

class MonkBalance extends Component
{
    public string $search = '';
    public string $filterType = '';
    public bool $onlyDebt = true;
    public array $expanded = [];

    public function toggleExpand(int $monkId): void
    {
        if (in_array($monkId, $this->expanded)) {
            $this->expanded = array_values(array_filter($this->expanded, fn($id) => $id !== $monkId));
        } else {
            $this->expanded[] = $monkId;
        }
    }

    public function markAllPaid(int $monkId): void
    {
        Absence::where('monk_id', $monkId)->where('is_paid', 0)->update(['is_paid' => 1]);
        $this->expanded = array_values(array_filter($this->expanded, fn($id) => $id !== $monkId));
        session()->flash('success', 'ໝາຍຈ່າຍທັງໝົດສຳເລັດ');
    }

    public function markPaid(int $absenceId): void
    {
        Absence::findOrFail($absenceId)->update(['is_paid' => 1]);
        session()->flash('success', 'ອັບເດດສະຖານະສຳເລັດ');
    }

    public function render()
    {
        $monks = Monk::where('status', 1)
            ->withCount(['absences as total_count'])
            ->withCount(['absences as unpaid_count' => fn($q) => $q->where('is_paid', 0)])
            ->withSum(['absences as unpaid_fine' => fn($q) => $q->where('is_paid', 0)], 'fine_amount')
            ->when($this->search, fn($q) =>
                $q->where(fn($inner) =>
                    $inner->where('name', 'like', "%{$this->search}%")
                          ->orWhere('surname', 'like', "%{$this->search}%")
                )
            )
            ->when($this->filterType, fn($q) => $q->where('type', $this->filterType))
            ->when($this->onlyDebt, fn($q) =>
                $q->whereHas('absences', fn($inner) => $inner->where('is_paid', 0))
            )
            ->orderByDesc('unpaid_fine')
            ->get();

        $expandedAbsences = [];
        foreach ($this->expanded as $monkId) {
            $expandedAbsences[$monkId] = Absence::with('fineRate')
                ->where('monk_id', $monkId)
                ->where('is_paid', 0)
                ->orderBy('absent_date')
                ->get();
        }

        $totalUnpaid   = Absence::where('is_paid', 0)->sum('fine_amount');
        $monksWithDebt = Monk::where('status', 1)
            ->whereHas('absences', fn($q) => $q->where('is_paid', 0))
            ->count();
        $unpaidCount   = Absence::where('is_paid', 0)->count();

        return view('livewire.balance.monk-balance', compact(
            'monks', 'expandedAbsences', 'totalUnpaid', 'monksWithDebt', 'unpaidCount'
        ));
    }
}
