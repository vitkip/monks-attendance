<?php

namespace App\Http\Controllers;

use App\Models\DutySchedule;
use Illuminate\Http\Request;

class DutyScheduleReportController extends Controller
{
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
                ->orderBy('duty_name')
                ->get()
                ->groupBy('day_of_week');
        }

        $onceGroups = collect();
        if ($type !== 'weekly') {
            $query = DutySchedule::with('monk')
                ->where('schedule_type', 'once')
                ->orderBy('duty_date')
                ->orderBy('duty_name');

            if ($from) $query->whereDate('duty_date', '>=', $from);
            if ($to)   $query->whereDate('duty_date', '<=', $to);

            $onceGroups = $query->get()
                ->groupBy(fn($d) => $d->duty_date->format('Y-m-d'));
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
