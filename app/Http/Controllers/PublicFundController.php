<?php

namespace App\Http\Controllers;

use App\Models\FundTransaction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicFundController extends Controller
{
    public function index(Request $request): Response
    {
        $filterType = (string) $request->query('type', '');

        $transactions = FundTransaction::query()
            ->when(in_array($filterType, ['income', 'expense'], true), fn ($q) => $q->where('type', $filterType))
            ->with(['monk:id,name,surname,type,photo'])
            ->latest('transaction_date')
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (FundTransaction $tx) => [
                'id' => $tx->id,
                'type' => $tx->type,
                'amount' => (float) $tx->amount,
                'transaction_date' => $tx->transaction_date->format('Y-m-d'),
                'transaction_date_month' => $tx->transaction_date->translatedFormat('M'),
                'transaction_date_day' => $tx->transaction_date->format('d'),
                'monk' => $tx->monk ? [
                    'full_name' => $tx->monk->full_name,
                    'type_label' => $tx->monk->type_label,
                    'photo_url' => $tx->monk->photo_url,
                ] : null,
                'party_label' => $tx->party_label,
                'description' => $tx->description,
            ]);

        $totals = FundTransaction::selectRaw("
            SUM(CASE WHEN type = 'income' THEN amount ELSE 0 END) as total_income,
            SUM(CASE WHEN type = 'expense' THEN amount ELSE 0 END) as total_expense
        ")->first();
        $totalIncomeAll = (float) ($totals->total_income ?? 0);
        $totalExpenseAll = (float) ($totals->total_expense ?? 0);

        return Inertia::render('Public/Fund/Index', [
            'type' => $filterType,
            'transactions' => $transactions,
            'totalIncomeAll' => $totalIncomeAll,
            'totalExpenseAll' => $totalExpenseAll,
            'balanceAll' => $totalIncomeAll - $totalExpenseAll,
            'fundAccount' => [
                'bank_name' => Setting::get('fund_bank_name'),
                'account_name' => Setting::get('fund_account_name'),
                'account_number' => Setting::get('fund_account_number'),
            ],
        ]);
    }
}
