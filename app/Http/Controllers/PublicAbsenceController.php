<?php

namespace App\Http\Controllers;

use App\Models\Monk;
use Illuminate\Support\Carbon;
use Inertia\Inertia;

class PublicAbsenceController extends Controller
{
    public function index()
    {
        $days = 90;
        $since = Carbon::now()->subDays($days)->startOfDay();

        $monks = Monk::where('status', 'active')
            ->withCount(['absences as absence_count' => fn ($q) => $q->where('absent_date', '>=', $since)])
            ->withSum(['absences as fine_total' => fn ($q) => $q->where('absent_date', '>=', $since)], 'fine_amount')
            ->orderByDesc('fine_total')
            ->orderByDesc('absence_count')
            ->orderBy('name')
            ->get();

        $maxAbsence = max(1, (int) $monks->max('absence_count'));
        $maxFine = max(1, (float) $monks->max('fine_total'));

        $monks = $monks->map(fn (Monk $monk) => [
            'id' => $monk->id,
            'full_name' => $monk->full_name,
            'type_label' => $monk->type_label,
            'photo_url' => $monk->photo_url,
            'temple' => $monk->temple,
            'absence_count' => (int) $monk->absence_count,
            'fine_total' => (float) $monk->fine_total,
        ])->values();

        return Inertia::render('Public/Absences/Index', [
            'monks' => $monks,
            'days' => $days,
            'maxAbsence' => $maxAbsence,
            'maxFine' => $maxFine,
            'periodStart' => $since->format('d/m/Y'),
            'periodEnd' => Carbon::now()->format('d/m/Y'),
        ]);
    }
}
