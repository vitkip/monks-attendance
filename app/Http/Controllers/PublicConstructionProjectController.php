<?php

namespace App\Http\Controllers;

use App\Models\ConstructionProject;
use App\Models\ConstructionTransaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicConstructionProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $status = (string) $request->query('status', '');

        $projects = ConstructionProject::query()
            ->when(in_array($status, ['ongoing', 'completed', 'paused'], true), fn ($q) => $q->where('status', $status))
            ->withSum('incomeTransactions as income_sum', 'amount')
            ->withSum('expenseTransactions as expense_sum', 'amount')
            ->withCount('transactions')
            ->latest()
            ->paginate(9)
            ->withQueryString()
            ->through(function (ConstructionProject $project) {
                $incomeSum = (float) ($project->income_sum ?? 0);
                $expenseSum = (float) ($project->expense_sum ?? 0);
                $percent = $project->target_amount && (float) $project->target_amount > 0
                    ? min(100, round($incomeSum / (float) $project->target_amount * 100, 1))
                    : null;

                return [
                    'id' => $project->id,
                    'name' => $project->name,
                    'status' => $project->status,
                    'start_date' => $project->start_date?->format('Y-m-d'),
                    'start_date_label' => $project->start_date?->translatedFormat('d/m/Y'),
                    'image_url' => $project->image_url,
                    'income_sum' => $incomeSum,
                    'expense_sum' => $expenseSum,
                    'transactions_count' => $project->transactions_count,
                    'percent' => $percent,
                    'target_amount' => $project->target_amount !== null ? (float) $project->target_amount : null,
                ];
            });

        $totalIncomeAll = (float) ConstructionTransaction::where('type', 'income')->sum('amount');
        $ongoingCount = ConstructionProject::where('status', 'ongoing')->count();

        $featuredProject = ConstructionProject::where('status', 'ongoing')
            ->whereNotNull('target_amount')
            ->latest('start_date')
            ->latest()
            ->first();

        $featured = $featuredProject ? [
            'id' => $featuredProject->id,
            'name' => $featuredProject->name,
            'image_url' => $featuredProject->image_url,
            'progress_percent' => $featuredProject->progress_percent,
            'total_income' => $featuredProject->total_income,
            'target_amount' => (float) $featuredProject->target_amount,
        ] : null;

        return Inertia::render('Public/ConstructionProjects/Index', [
            'status' => $status,
            'projects' => $projects,
            'statuses' => ConstructionProject::statuses(),
            'totalIncomeAll' => $totalIncomeAll,
            'ongoingCount' => $ongoingCount,
            'featured' => $featured,
        ]);
    }

    public function show(ConstructionProject $project): Response
    {
        $transactions = $project->transactions()
            ->latest('transaction_date')
            ->latest()
            ->paginate(15)
            ->through(fn (ConstructionTransaction $tx) => [
                'id' => $tx->id,
                'type' => $tx->type,
                'amount' => (float) $tx->amount,
                'transaction_date' => $tx->transaction_date->format('Y-m-d'),
                'transaction_date_month' => $tx->transaction_date->translatedFormat('M'),
                'transaction_date_day' => $tx->transaction_date->format('d'),
                'description' => $tx->description,
                'image_url' => $tx->image_url,
            ]);

        return Inertia::render('Public/ConstructionProjects/Show', [
            'project' => [
                'id' => $project->id,
                'name' => $project->name,
                'description' => $project->description,
                'status' => $project->status,
                'start_date' => $project->start_date?->format('Y-m-d'),
                'start_date_label' => $project->start_date?->translatedFormat('d F Y'),
                'image_url' => $project->image_url,
                'total_income' => $project->total_income,
                'total_expense' => $project->total_expense,
                'balance' => $project->balance,
                'progress_percent' => $project->progress_percent,
                'target_amount' => $project->target_amount !== null ? (float) $project->target_amount : null,
            ],
            'transactions' => $transactions,
            'statuses' => ConstructionProject::statuses(),
        ]);
    }
}
