<?php

namespace App\Http\Controllers;

use App\Models\ConstructionProject;
use App\Models\ConstructionTransaction;
use Illuminate\Http\Request;

class PublicConstructionProjectController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', '');

        $projects = ConstructionProject::query()
            ->when(in_array($status, ['ongoing', 'completed', 'paused'], true), fn($q) => $q->where('status', $status))
            ->withSum('incomeTransactions as income_sum', 'amount')
            ->withSum('expenseTransactions as expense_sum', 'amount')
            ->withCount('transactions')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $totalIncomeAll = (float) ConstructionTransaction::where('type', 'income')->sum('amount');
        $ongoingCount = ConstructionProject::where('status', 'ongoing')->count();

        $featured = ConstructionProject::where('status', 'ongoing')
            ->whereNotNull('target_amount')
            ->latest('start_date')
            ->latest()
            ->first();

        return view('public.construction-projects.index', compact(
            'projects', 'status', 'totalIncomeAll', 'ongoingCount', 'featured'
        ));
    }

    public function show(ConstructionProject $project)
    {
        $transactions = $project->transactions()
            ->latest('transaction_date')
            ->latest()
            ->paginate(15);

        return view('public.construction-projects.show', compact('project', 'transactions'));
    }
}
