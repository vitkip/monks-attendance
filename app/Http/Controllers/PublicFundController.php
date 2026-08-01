<?php

namespace App\Http\Controllers;

use App\Models\FundTransaction;
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
            ->with('monk')
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

        $totalIncomeAll = (float) FundTransaction::where('type', 'income')->sum('amount');
        $totalExpenseAll = (float) FundTransaction::where('type', 'expense')->sum('amount');

        return Inertia::render('Public/Fund/Index', [
            'type' => $filterType,
            'transactions' => $transactions,
            'totalIncomeAll' => $totalIncomeAll,
            'totalExpenseAll' => $totalExpenseAll,
            'balanceAll' => $totalIncomeAll - $totalExpenseAll,
        ]);
    }
}
