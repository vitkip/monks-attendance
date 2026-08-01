<?php

namespace App\Http\Controllers;

use App\Models\ElectricityBill;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $data = $this->validateBill($request, isEdit: false);
        $data['image'] = $request->file('image')->store('electricity-bills', 'public');
        $data['user_id'] = auth()->id();

        ElectricityBill::create($data);

        return back()->with('success', 'ເພີ່ມໃບບິນຄ່າໄຟຟ້າສຳເລັດ');
    }

    public function update(Request $request, ElectricityBill $electricityBill): RedirectResponse
    {
        $data = $this->validateBill($request, isEdit: true);

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

    private function validateBill(Request $request, bool $isEdit): array
    {
        $rules = [
            'account_number' => 'required|string|max:50',
            'province' => 'required|string|in:' . implode(',', ElectricityBill::provinces()),
            'customer_name' => 'required|string|max:200',
            'bill_month' => 'required|date_format:Y-m',
            'amount' => 'required|numeric|min:0',
            'image' => $isEdit ? 'nullable|image|max:4096' : 'required|image|max:4096',
        ];

        $messages = [
            'account_number.required' => 'ກະລຸນາປ້ອນເລກບັນຊີຜູ້ໃຊ້ໄຟຟ້າ',
            'province.required' => 'ກະລຸນາເລືອກແຂວງ',
            'customer_name.required' => 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ໄຟຟ້າ',
            'bill_month.required' => 'ກະລຸນາເລືອກເດືອນຂອງໃບບິນ',
            'amount.required' => 'ກະລຸນາປ້ອນຈຳນວນເງິນ',
            'amount.numeric' => 'ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ',
            'image.required' => 'ກະລຸນາອັບໂຫລດຮູບພາບໃບບິນ',
        ];

        $validated = Validator::make($request->all(), $rules, $messages)->validate();

        return [
            'account_number' => $validated['account_number'],
            'province' => $validated['province'],
            'customer_name' => $validated['customer_name'],
            'bill_month' => $validated['bill_month'] . '-01',
            'amount' => $validated['amount'],
        ];
    }
}
