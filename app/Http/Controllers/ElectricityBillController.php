<?php

namespace App\Http\Controllers;

use App\Models\ElectricityBill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class ElectricityBillController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');
        $filterProvince = (string) $request->query('province', '');
        $filterMonth = (string) $request->query('month', '');

        $bills = ElectricityBill::query()
            ->when($filterProvince !== '', fn ($q) => $q->where('province', $filterProvince))
            ->when($filterMonth !== '', function ($q) use ($filterMonth) {
                [$y, $m] = explode('-', $filterMonth);
                $q->whereYear('bill_month', $y)->whereMonth('bill_month', $m);
            })
            ->when($search, fn ($q) => $q->where(fn ($q2) => $q2->where('account_number', 'like', "%{$search}%")
                ->orWhere('customer_name', 'like', "%{$search}%")
            ))
            ->with('recordedBy')
            ->latest('bill_month')
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn (ElectricityBill $bill) => [
                'id' => $bill->id,
                'account_number' => $bill->account_number,
                'province' => $bill->province,
                'customer_name' => $bill->customer_name,
                'bill_month' => $bill->bill_month->format('Y-m'),
                'bill_month_label' => $bill->bill_month->translatedFormat('m/Y'),
                'amount' => (float) $bill->amount,
                'image' => $bill->image,
                'image_url' => $bill->image_url,
                'recorded_by_name' => $bill->recordedBy?->name,
                'created_at' => $bill->created_at->format('d/m/Y'),
            ]);

        return Inertia::render('ElectricityBills/Index', [
            'filters' => ['search' => $search, 'province' => $filterProvince, 'month' => $filterMonth],
            'bills' => $bills,
            'provinces' => ElectricityBill::provinces(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateBill($request, currentBill: null);
        $data['image'] = $request->file('image')->store('electricity-bills', 'public');
        $data['user_id'] = auth()->id();

        ElectricityBill::create($data);

        return back()->with('success', 'ເພີ່ມໃບບິນຄ່າໄຟຟ້າສຳເລັດ');
    }

    public function update(Request $request, ElectricityBill $electricityBill): RedirectResponse
    {
        $data = $this->validateBill($request, currentBill: $electricityBill);

        if ($request->hasFile('image')) {
            if ($electricityBill->image) {
                Storage::disk('public')->delete($electricityBill->image);
            }

            $data['image'] = $request->file('image')->store('electricity-bills', 'public');
        }

        $electricityBill->update($data);

        return back()->with('success', 'ແກ້ໄຂໃບບິນຄ່າໄຟຟ້າສຳເລັດ');
    }

    public function destroy(ElectricityBill $electricityBill): RedirectResponse
    {
        if ($electricityBill->image) {
            Storage::disk('public')->delete($electricityBill->image);
        }

        $electricityBill->delete();

        return back()->with('success', 'ລຶບໃບບິນຄ່າໄຟຟ້າສຳເລັດ');
    }

    private function validateBill(Request $request, ?ElectricityBill $currentBill = null): array
    {
        $isEdit = $currentBill !== null;

        $rules = [
            'account_number' => 'required|string|max:50',
            'province' => 'required|string|in:' . implode(',', ElectricityBill::provinces()),
            'customer_name' => 'required|string|max:200',
            'bill_month' => 'required|date_format:Y-m',
            'amount' => 'required|numeric|min:0',
            'image' => $isEdit ? 'nullable|mimes:jpg,jpeg,png,gif,webp,pdf|max:4096' : 'required|mimes:jpg,jpeg,png,gif,webp,pdf|max:4096',
        ];

        $messages = [
            'account_number.required' => 'ກະລຸນາປ້ອນເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້າ',
            'province.required' => 'ກະລຸນາເລືອກແຂວງ',
            'customer_name.required' => 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ໄຟຟ້າ',
            'bill_month.required' => 'ກະລຸນາເລືອກເດືອນຂອງໃບບິນ',
            'amount.required' => 'ກະລຸນາປ້ອນຈຳນວນເງິນ',
            'amount.numeric' => 'ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ',
            'image.required' => 'ກະລຸນາອັບໂຫລດຮູບພາບ ຫຼື ໄຟລ໌ PDF ໃບບິນ',
            'image.mimes' => 'ຮອງຮັບສະເພາະໄຟລ໌ JPG, PNG, GIF, WebP ຫຼື PDF ເທົ່ານັ້ນ',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        $validator->after(function ($validator) use ($request, $currentBill) {
            $accountNumber = trim((string) $request->input('account_number'));
            $billMonth = (string) $request->input('bill_month');

            if ($accountNumber !== '' && $billMonth !== '' && preg_match('/^\d{4}-\d{2}$/', $billMonth)) {
                [$year, $month] = explode('-', $billMonth);

                $exists = ElectricityBill::query()
                    ->where('account_number', $accountNumber)
                    ->whereYear('bill_month', (int) $year)
                    ->whereMonth('bill_month', (int) $month)
                    ->when($currentBill, fn ($q) => $q->where('id', '!=', $currentBill->id))
                    ->exists();

                if ($exists) {
                    $validator->errors()->add('account_number', 'ເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້ານີ້ ມີການບັນທຶກໃບບິນໃນເດືອນນີ້ແລ້ວ');
                }
            }
        });

        $validated = $validator->validate();

        return [
            'account_number' => $validated['account_number'],
            'province' => $validated['province'],
            'customer_name' => $validated['customer_name'],
            'bill_month' => $validated['bill_month'] . '-01',
            'amount' => $validated['amount'],
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

        $promptText = 'You are an expert OCR parser for Lao electricity bills (Electricite du Laos / EDL). Examine this bill image or PDF document and extract the following details accurately: 1. "amount": Total payable amount in Kip (numeric float or integer only, no commas or currency symbols). 2. "account_number": Electricity account number / ເລກບັນຊີ / ລະຫັດຜູ້ໃຊ້ໄຟຟ້າ. 3. "customer_name": Customer name / ຊື່ຜູ້ໃຊ້ໄຟ (string). 4. "province": Match one of these exact Lao province strings if found: ["ນະຄອນຫຼວງວຽງຈັນ", "ຜົ້ງສາລີ", "ຫຼວງນ້ຳທາ", "ບໍ່ແກ້ວ", "ອຸດົມໄຊ", "ໄຊຍະບູລີ", "ຫຼວງພະບາງ", "ຫົວພັນ", "ຊຽງຂວາງ", "ວຽງຈັນ", "ບໍລິຄຳໄຊ", "ຄຳມ່ວນ", "ສະຫວັນນະເຂດ", "ສາລະວັນ", "ເຊກອງ", "ຈຳປາສັກ", "ອັດຕະປື", "ໄຊສົມບູນ"]. Default to empty string if not found. 5. "bill_month": Bill month in YYYY-MM format. Return ONLY a raw JSON object with keys: "amount", "account_number", "customer_name", "province", "bill_month". No markdown tags, no explanation.';

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
                Log::error('Anthropic API scan error: ' . ($response ? $response->body() : 'No response'));
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
                    'account_number' => (string) ($parsed['account_number'] ?? ''),
                    'customer_name' => (string) ($parsed['customer_name'] ?? ''),
                    'province' => (string) ($parsed['province'] ?? ''),
                    'bill_month' => (string) ($parsed['bill_month'] ?? ''),
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error('AI scan exception: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'ເກີດຂໍ້ຜິດພາດໃນການປະມວນຜົນ: ' . $e->getMessage(),
            ], 500);
        }
    }
}
