<?php

namespace App\Http\Controllers;

use App\Models\ElectricityBill;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicElectricityBillController extends Controller
{
    public function index(Request $request): Response
    {
        $availableYears = \Illuminate\Support\Facades\Cache::remember('electricity_bills:available_years', 3600, function () {
            return ElectricityBill::selectRaw('YEAR(bill_month) as year')
                ->distinct()
                ->orderByDesc('year')
                ->pluck('year')
                ->all();
        });
        $availableYears = collect($availableYears);

        $year = (int) ($request->query('year') ?: $availableYears->first() ?: now()->year);

        $bills = ElectricityBill::whereYear('bill_month', $year)
            ->select(['id', 'account_number', 'province', 'customer_name', 'bill_month', 'amount', 'image'])
            ->orderByDesc('bill_month')
            ->orderByDesc('id')
            ->get();

        $billsByMonth = $bills->groupBy(fn ($bill) => $bill->bill_month->format('Y-m'))
            ->map(function ($group, $monthKey) {
                return [
                    'month' => $monthKey,
                    'month_num' => (int) $group->first()->bill_month->format('n'),
                    'total' => (float) $group->sum('amount'),
                    'count' => $group->count(),
                    'bills' => $group->map(fn (ElectricityBill $bill) => [
                        'id' => $bill->id,
                        'account_number' => $bill->account_number,
                        'province' => $bill->province,
                        'customer_name' => $bill->customer_name,
                        'bill_month' => $bill->bill_month->format('Y-m'),
                        'bill_month_label' => $bill->bill_month->format('m/Y'),
                        'amount' => (float) $bill->amount,
                        'image' => $bill->image,
                        'image_url' => $bill->image_url,
                    ])->values(),
                ];
            })
            ->values();

        $monthlyTotals = $bills
            ->groupBy(fn ($bill) => (int) $bill->bill_month->format('n'))
            ->map(fn ($group) => (float) $group->sum('amount'));

        $monthly = collect(range(1, 12))->map(fn ($m) => (float) ($monthlyTotals[$m] ?? 0))->values();

        $totalYear = $monthly->sum();
        $countYear = $bills->count();
        $totalAllTime = (float) \Illuminate\Support\Facades\Cache::remember('electricity_bills:total_all_time', 3600, function () {
            return ElectricityBill::sum('amount');
        });

        return Inertia::render('Public/ElectricityBills/Index', [
            'billsByMonth' => $billsByMonth,
            'availableYears' => $availableYears->values(),
            'year' => $year,
            'monthly' => $monthly,
            'totalYear' => $totalYear,
            'countYear' => $countYear,
            'totalAllTime' => $totalAllTime,
        ]);
    }
}
