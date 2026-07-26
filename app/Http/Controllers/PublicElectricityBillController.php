<?php

namespace App\Http\Controllers;

use App\Models\ElectricityBill;
use Illuminate\Http\Request;

class PublicElectricityBillController extends Controller
{
    public function index(Request $request)
    {
        $availableYears = ElectricityBill::selectRaw('YEAR(bill_month) as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year');

        $year = (int) ($request->query('year') ?: $availableYears->first() ?: now()->year);

        $bills = ElectricityBill::whereYear('bill_month', $year)
            ->orderByDesc('bill_month')
            ->orderByDesc('id')
            ->get();

        $billsByMonth = $bills->groupBy(fn ($bill) => $bill->bill_month->format('Y-m'));

        $monthlyTotals = $bills
            ->groupBy(fn ($bill) => (int) $bill->bill_month->format('n'))
            ->map(fn ($group) => (float) $group->sum('amount'));

        $monthly = collect(range(1, 12))->map(fn($m) => (float) ($monthlyTotals[$m] ?? 0));

        $totalYear = $monthly->sum();
        $countYear = $bills->count();
        $totalAllTime = ElectricityBill::sum('amount');

        return view('public.electricity-bills.index', compact(
            'billsByMonth', 'availableYears', 'year', 'monthly', 'totalYear', 'countYear', 'totalAllTime'
        ));
    }
}
