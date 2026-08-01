<?php

namespace App\Http\Controllers;

use App\Models\ConstructionProject;
use App\Models\ConstructionTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ConstructionProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');
        $filterStatus = (string) $request->query('status', '');

        $projects = ConstructionProject::query()
            ->when($filterStatus !== '', fn ($q) => $q->where('status', $filterStatus))
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%"))
            ->withSum('incomeTransactions as income_sum', 'amount')
            ->withSum('expenseTransactions as expense_sum', 'amount')
            ->withCount('transactions')
            ->with('recordedBy')
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
                    'balance' => $incomeSum - $expenseSum,
                    'percent' => $percent,
                    'target_amount' => $project->target_amount !== null ? (float) $project->target_amount : null,
                ];
            });

        return Inertia::render('ConstructionProjects/Index', [
            'filters' => ['search' => $search, 'status' => $filterStatus],
            'projects' => $projects,
            'statuses' => ConstructionProject::statuses(),
            'totalIncomeAll' => (float) ConstructionTransaction::where('type', 'income')->sum('amount'),
            'totalExpenseAll' => (float) ConstructionTransaction::where('type', 'expense')->sum('amount'),
            'ongoingCount' => ConstructionProject::where('status', 'ongoing')->count(),
        ]);
    }

    public function show(Request $request, ConstructionProject $constructionProject): Response
    {
        $filterType = (string) $request->query('type', '');

        $transactions = $constructionProject->transactions()
            ->when($filterType !== '', fn ($q) => $q->where('type', $filterType))
            ->with('recordedBy')
            ->latest('transaction_date')
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (ConstructionTransaction $tx) => [
                'id' => $tx->id,
                'type' => $tx->type,
                'amount' => (float) $tx->amount,
                'transaction_date' => $tx->transaction_date->format('Y-m-d'),
                'transaction_date_month' => $tx->transaction_date->translatedFormat('M'),
                'transaction_date_day' => $tx->transaction_date->format('d'),
                'description' => $tx->description,
                'image' => $tx->image,
                'image_url' => $tx->image_url,
                'recorded_by_name' => $tx->recordedBy?->name,
            ]);

        return Inertia::render('ConstructionProjects/Show', [
            'filters' => ['type' => $filterType],
            'project' => [
                'id' => $constructionProject->id,
                'name' => $constructionProject->name,
                'description' => $constructionProject->description,
                'status' => $constructionProject->status,
                'start_date' => $constructionProject->start_date?->format('Y-m-d'),
                'start_date_label' => $constructionProject->start_date?->translatedFormat('d M Y'),
                'image_url' => $constructionProject->image_url,
                'total_income' => $constructionProject->total_income,
                'total_expense' => $constructionProject->total_expense,
                'balance' => $constructionProject->balance,
                'progress_percent' => $constructionProject->progress_percent,
                'target_amount' => $constructionProject->target_amount !== null ? (float) $constructionProject->target_amount : null,
            ],
            'transactions' => $transactions,
            'statuses' => ConstructionProject::statuses(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateProject($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('construction-projects', 'public');
        }

        $data['user_id'] = auth()->id();
        ConstructionProject::create($data);

        return back()->with('success', 'ເພີ່ມໂຄງການສຳເລັດ');
    }

    public function update(Request $request, ConstructionProject $constructionProject): RedirectResponse
    {
        $data = $this->validateProject($request);

        if ($request->hasFile('image')) {
            if ($constructionProject->image) {
                Storage::disk('public')->delete($constructionProject->image);
            }
            $data['image'] = $request->file('image')->store('construction-projects', 'public');
        }

        $constructionProject->update($data);

        return back()->with('success', 'ແກ້ໄຂໂຄງການສຳເລັດ');
    }

    public function destroy(ConstructionProject $constructionProject): RedirectResponse
    {
        $constructionProject->transactions()->whereNotNull('image')->get()->each(function ($tx) {
            Storage::disk('public')->delete($tx->image);
        });

        if ($constructionProject->image) {
            Storage::disk('public')->delete($constructionProject->image);
        }

        $constructionProject->delete();

        return redirect()->route('construction-projects.index')->with('success', 'ລຶບໂຄງການສຳເລັດ');
    }

    public function storeTransaction(Request $request, ConstructionProject $constructionProject): RedirectResponse
    {
        $data = $this->validateTransaction($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('construction-transactions', 'public');
        }

        $data['construction_project_id'] = $constructionProject->id;
        $data['user_id'] = auth()->id();

        ConstructionTransaction::create($data);

        return back()->with('success', 'ເພີ່ມລາຍການສຳເລັດ');
    }

    public function updateTransaction(Request $request, ConstructionProject $constructionProject, ConstructionTransaction $transaction): RedirectResponse
    {
        $data = $this->validateTransaction($request);

        if ($request->hasFile('image')) {
            if ($transaction->image) {
                Storage::disk('public')->delete($transaction->image);
            }
            $data['image'] = $request->file('image')->store('construction-transactions', 'public');
        }

        $transaction->update($data);

        return back()->with('success', 'ແກ້ໄຂລາຍການສຳເລັດ');
    }

    public function destroyTransaction(ConstructionProject $constructionProject, ConstructionTransaction $transaction): RedirectResponse
    {
        if ($transaction->image) {
            Storage::disk('public')->delete($transaction->image);
        }
        $transaction->delete();

        return back()->with('success', 'ລຶບລາຍການສຳເລັດ');
    }

    private function validateProject(Request $request): array
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:200',
            'description' => 'nullable|string|max:2000',
            'start_date' => 'nullable|date',
            'target_amount' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|max:4096',
            'status' => 'required|in:' . implode(',', array_keys(ConstructionProject::statuses())),
        ], [
            'name.required' => 'ກະລຸນາປ້ອນຊື່ໂຄງການ',
            'status.required' => 'ກະລຸນາເລືອກສະຖານະໂຄງການ',
        ])->validate();

        return [
            'name' => $validated['name'],
            'description' => $validated['description'] ?: null,
            'start_date' => $validated['start_date'] ?: null,
            'target_amount' => $validated['target_amount'] !== null && $validated['target_amount'] !== '' ? $validated['target_amount'] : null,
            'status' => $validated['status'],
        ];
    }

    private function validateTransaction(Request $request): array
    {
        $validated = Validator::make($request->all(), [
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:4096',
        ], [
            'type.required' => 'ກະລຸນາເລືອກປະເພດລາຍການ',
            'amount.required' => 'ກະລຸນາປ້ອນຈຳນວນເງິນ',
            'amount.numeric' => 'ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ',
            'transaction_date.required' => 'ກະລຸນາເລືອກວັນທີ',
            'image.image' => 'ໄຟລ໌ຕ້ອງເປັນຮູບພາບ',
        ])->validate();

        return [
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'] ?: null,
        ];
    }
}
