<?php

namespace App\Http\Controllers;

use App\Models\Absence;
use App\Models\Monk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BalanceController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');
        $filterType = (string) $request->query('type', '');
        $onlyDebt = $request->query('onlyDebt', '1') !== '0';

        $monks = Monk::where('status', 'active')
            ->select(['id', 'name', 'surname', 'type', 'photo', 'temple', 'pansa'])
            ->withCount(['absences as total_count'])
            ->withCount(['absences as unpaid_count' => fn ($q) => $q->where('is_paid', 0)])
            ->withSum(['absences as unpaid_fine' => fn ($q) => $q->where('is_paid', 0)], 'fine_amount')
            ->with(['absences' => fn ($q) => $q->where('is_paid', 0)->select(['id', 'monk_id', 'fine_rate_id', 'absent_date', 'fine_amount', 'reason'])->with('fineRate:id,name')->orderBy('absent_date')])
            ->when($search, fn ($q) => $q->where(fn ($inner) => $inner->where('name', 'like', "%{$search}%")
                ->orWhere('surname', 'like', "%{$search}%")
            ))
            ->when($filterType, fn ($q) => $q->where('type', $filterType))
            ->when($onlyDebt, fn ($q) => $q->whereHas('absences', fn ($inner) => $inner->where('is_paid', 0)))
            ->orderByDesc('unpaid_fine')
            ->get();

        $unpaidStats = Absence::where('is_paid', 0)
            ->selectRaw('SUM(fine_amount) as total_unpaid, COUNT(*) as unpaid_count')
            ->first();
        $totalUnpaid = (float) ($unpaidStats->total_unpaid ?? 0);
        $unpaidCount = (int) ($unpaidStats->unpaid_count ?? 0);

        $monksWithDebt = Monk::where('status', 'active')
            ->whereHas('absences', fn ($q) => $q->where('is_paid', 0))
            ->count();

        return Inertia::render('Balance/Index', [
            'filters' => ['search' => $search, 'type' => $filterType, 'onlyDebt' => $onlyDebt],
            'monks' => $monks->map(fn ($monk) => [
                'id' => $monk->id,
                'full_name' => $monk->full_name,
                'type' => $monk->type,
                'type_label' => $monk->type_label,
                'photo_url' => $monk->photo_url,
                'temple' => $monk->temple,
                'pansa' => $monk->pansa,
                'total_count' => $monk->total_count,
                'unpaid_count' => $monk->unpaid_count,
                'unpaid_fine' => (float) ($monk->unpaid_fine ?? 0),
                'unpaid_absences' => $monk->absences->map(fn ($a) => [
                    'id' => $a->id,
                    'absent_date' => $a->absent_date->format('d/m/Y'),
                    'fine_rate_name' => $a->fineRate->name,
                    'reason' => $a->reason,
                    'fine_amount' => (float) $a->fine_amount,
                ]),
            ]),
            'totalUnpaid' => (float) $totalUnpaid,
            'monksWithDebt' => $monksWithDebt,
            'unpaidCount' => $unpaidCount,
        ]);
    }

    public function markAllPaid(Monk $monk): RedirectResponse
    {
        Absence::where('monk_id', $monk->id)->where('is_paid', 0)->update(['is_paid' => 1]);

        return back()->with('success', 'ໝາຍຈ່າຍທັງໝົດສຳເລັດ');
    }
}
