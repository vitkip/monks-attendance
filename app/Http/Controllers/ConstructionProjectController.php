<?php

namespace App\Http\Controllers;

use App\Models\ConstructionProject;
use App\Models\ConstructionTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
                'show_transactions' => (bool) $constructionProject->show_transactions,
            ],
            'transactions' => $transactions,
            'statuses' => ConstructionProject::statuses(),
        ]);
    }

    public function toggleTransactions(ConstructionProject $constructionProject): RedirectResponse
    {
        $constructionProject->update(['show_transactions' => ! $constructionProject->show_transactions]);

        return back()->with('success', $constructionProject->show_transactions
            ? 'ເປີດການສະແດງລາຍການໃນໜ້າສາທາລະນະແລ້ວ'
            : 'ປິດການສະແດງລາຍການໃນໜ້າສາທາລະນະແລ້ວ');
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
            'image' => 'nullable|mimes:jpg,jpeg,png,gif,webp,pdf|max:4096',
        ], [
            'type.required' => 'ກະລຸນາເລືອກປະເພດລາຍການ',
            'amount.required' => 'ກະລຸນາປ້ອນຈຳນວນເງິນ',
            'amount.numeric' => 'ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ',
            'transaction_date.required' => 'ກະລຸນາເລືອກວັນທີ',
            'image.mimes' => 'ຮອງຮັບສະເພາະໄຟລ໌ JPG, PNG, GIF, WebP ຫຼື PDF ເທົ່ານັ້ນ',
        ])->validate();

        return [
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'description' => $validated['description'] ?: null,
        ];
    }

    public function scanAi(Request $request)
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:4096',
        ], [
            'image.required' => 'ກະລຸນາເລືອກໄຟລ໌ເພື່ອສະແກນ',
            'image.mimes' => 'ຮອງຮັບສະເພາະໄຟລ໌ JPG, PNG, GIF, WebP ຫຼື PDF ເທົ່ານັ້ນ',
            'image.max' => 'ຂະໜາດໄຟລ໌ຕ້ອງບໍ່ເກີນ 4MB',
        ]);

        $file = $request->file('image');
        $mimeType = $file->getMimeType();

        if ($file->getClientOriginalExtension() === 'pdf' || $mimeType === 'application/pdf') {
            $mimeType = 'application/pdf';
        }

        $base64Data = base64_encode(file_get_contents($file->getRealPath()));
        $apiKey = config('services.anthropic.api_key');

        if (!$apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'ບໍ່ພົບ API Key ຂອງ Anthropic ໃນລະບົບ',
            ], 400);
        }

        $sourceContent = [
            'type' => $mimeType === 'application/pdf' ? 'document' : 'image',
            'source' => [
                'type' => 'base64',
                'media_type' => $mimeType,
                'data' => $base64Data,
            ],
        ];

        $promptText = 'You are an expert OCR parser for construction expense receipts, invoices, material store bills, and payment records in Lao and English. Examine this receipt image or PDF document and extract the following details accurately: 1. "amount": Total payable / paid amount in Kip or currency (numeric float or integer only, no commas or currency symbols). 2. "transaction_date": Date on receipt in YYYY-MM-DD format (if not found or invalid, return empty string). 3. "description": Brief item summary or vendor/store name and items bought in Lao (string, e.g. "ຄ່າຊີມັງ 10 ຖົງ", "ຄ່າເຫຼັກ 12mm"). 4. "type": Return "expense" for purchase/expense receipt, or "income" for donation/income receipt. Default to "expense". Return ONLY a raw JSON object with keys: "amount", "transaction_date", "description", "type". No markdown tags, no explanation.';

        $modelsToTry = [
            'claude-sonnet-4-6',
            'claude-haiku-4-5-20251001',
            'claude-sonnet-4-5-20250929',
            'claude-3-5-sonnet-latest',
        ];
        $response = null;

        try {
            foreach ($modelsToTry as $modelName) {
                $res = Http::withHeaders([
                    'x-api-key' => $apiKey,
                    'anthropic-version' => '2023-06-01',
                    'anthropic-beta' => 'pdfs-2024-09-25',
                    'content-type' => 'application/json',
                ])->timeout(30)->post('https://api.anthropic.com/v1/messages', [
                    'model' => $modelName,
                    'max_tokens' => 1000,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                $sourceContent,
                                [
                                    'type' => 'text',
                                    'text' => $promptText,
                                ],
                            ],
                        ],
                    ],
                ]);

                $response = $res;
                if ($res->successful()) {
                    break;
                }
            }

            if (!$response || !$response->successful()) {
                Log::error('Anthropic API scan error (Construction): ' . ($response ? $response->body() : 'No response'));
                return response()->json([
                    'success' => false,
                    'message' => 'ການຕິດຕໍ່ AI ລົ້ມເຫຼວ: ' . ($response ? ($response->json('error.message') ?? $response->body()) : 'No response'),
                ], 500);
            }

            $contentBlocks = $response->json('content', []);
            $rawContent = '';
            foreach ($contentBlocks as $block) {
                if (($block['type'] ?? '') === 'text') {
                    $rawContent .= $block['text'] ?? '';
                }
            }
            $rawContent = trim($rawContent);
            $cleanJson = preg_replace('/^```(?:json)?\s*|\s*```$/i', '', $rawContent);
            $cleanJson = trim($cleanJson);

            $parsed = json_decode($cleanJson, true);

            if (!is_array($parsed)) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI ບໍ່ສາມາດອ່ານໂຄງສ້າງຂໍ້ມູນໄດ້',
                    'raw' => $rawContent,
                ], 422);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'amount' => isset($parsed['amount']) ? (float) preg_replace('/[^0-9.]/', '', (string)$parsed['amount']) : '',
                    'transaction_date' => (string) ($parsed['transaction_date'] ?? ''),
                    'description' => (string) ($parsed['description'] ?? ''),
                    'type' => in_array(($parsed['type'] ?? ''), ['income', 'expense']) ? $parsed['type'] : 'expense',
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('AI scan exception (Construction): ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ເກີດຂໍ້ຜິດພາດໃນການປະມວນຜົນ: ' . $e->getMessage(),
            ], 500);
        }
    }
}
