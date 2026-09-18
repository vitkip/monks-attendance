<?php

namespace App\Http\Controllers;

use App\Models\DutySchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class DutyScheduleReportController extends Controller
{
    /**
     * ລຽງລຳດັບ: ພຣະສົງກ່ອນ ແລ້ວຄ່ອຍສາມະເນນ, ພັນສາຫຼາຍຂຶ້ນກ່ອນ.
     */
    protected function sortBySeniority(Collection $duties): Collection
    {
        return $duties->sortBy([
            fn ($a, $b) => $this->typeRank($a->monk) <=> $this->typeRank($b->monk),
            fn ($a, $b) => (int) ($b->monk->pansa ?? 0) <=> (int) ($a->monk->pansa ?? 0),
            fn ($a, $b) => strcmp((string) $a->monk?->full_name, (string) $b->monk?->full_name),
        ])->values();
    }

    protected function typeRank(?object $monk): int
    {
        return match ($monk?->type) {
            'monk'   => 0,
            'novice' => 1,
            'nun'    => 2,
            default  => 3,
        };
    }

    public function __invoke(Request $request)
    {
        $type = $request->input('type', 'all');
        $from = $request->input('from');
        $to   = $request->input('to');

        $weeklyGroups = collect();
        if ($type !== 'once') {
            $weeklyGroups = DutySchedule::with('monk')
                ->where('schedule_type', 'weekly')
                ->orderBy('day_of_week')
                ->get()
                ->groupBy('day_of_week')
                ->map(fn ($duties) => $this->sortBySeniority($duties));
        }

        $onceGroups = collect();
        if ($type !== 'weekly') {
            $query = DutySchedule::with('monk')
                ->where('schedule_type', 'once')
                ->orderBy('duty_date');

            if ($from) $query->whereDate('duty_date', '>=', $from);
            if ($to)   $query->whereDate('duty_date', '<=', $to);

            $onceGroups = $query->get()
                ->groupBy(fn($d) => $d->duty_date->format('Y-m-d'))
                ->map(fn ($duties) => $this->sortBySeniority($duties));
        }

        $dayNames    = DutySchedule::$dayNames;
        $totalWeekly = DutySchedule::where('schedule_type', 'weekly')->count();
        $totalOnce   = DutySchedule::where('schedule_type', 'once')->count();
        $totalMonks  = DutySchedule::distinct('monk_id')->count('monk_id');
        $generatedAt = now();

        return view('duty-schedules.report', compact(
            'weeklyGroups', 'onceGroups', 'dayNames',
            'totalWeekly', 'totalOnce', 'totalMonks',
            'generatedAt', 'type', 'from', 'to'
        ));
    }
}
