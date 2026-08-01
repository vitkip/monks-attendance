<?php

namespace App\Http\Controllers;

use App\Models\FundTransaction;
use App\Models\Monk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class FundController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');
        $filterType = (string) $request->query('type', '');
        $filterMonth = (string) $request->query('month', '');

        $transactions = FundTransaction::query()
            ->when($filterType !== '', fn ($q) => $q->where('type', $filterType))
            ->when($filterMonth !== '', function ($q) use ($filterMonth) {
                [$y, $m] = explode('-', $filterMonth);
                $q->whereYear('transaction_date', $y)->whereMonth('transaction_date', $m);
            })
            ->when($search, fn ($q) => $q->where(fn ($q2) => $q2->where('description', 'like', "%{$search}%")
                ->orWhere('party_name', 'like', "%{$search}%")
                ->orWhereHas('monk', fn ($q3) => $q3->where('name', 'like', "%{$search}%")->orWhere('surname', 'like', "%{$search}%"))
            ))
            ->with(['monk', 'recordedBy'])
            ->latest('transaction_date')
            ->latest()
            ->paginate(15)
            ->withQueryString()
            ->through(fn (FundTransaction $tx) => $this->transformTransaction($tx));

        return Inertia::render('Fund/Index', [
            'filters' => ['search' => $search, 'type' => $filterType, 'month' => $filterMonth],
            'transactions' => $transactions,
            'types' => FundTransaction::types(),
            'monks' => Monk::orderBy('name')->get()->map(fn (Monk $m) => [
                'id' => $m->id,
                'full_name' => $m->full_name,
                'type_label' => $m->type_label,
            ]),
            'totalIncomeAll' => (float) FundTransaction::where('type', 'income')->sum('amount'),
            'totalExpenseAll' => (float) FundTransaction::where('type', 'expense')->sum('amount'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateTransaction($request);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('fund-transactions', 'public');
        }

        $data['user_id'] = auth()->id();
        FundTransaction::create($data);

        return back()->with('success', 'ເພີ່ມລາຍການສຳເລັດ');
    }

    public function update(Request $request, FundTransaction $fund): RedirectResponse
    {
        $data = $this->validateTransaction($request);

        if ($request->hasFile('image')) {
            if ($fund->image) {
                Storage::disk('public')->delete($fund->image);
            }
            $data['image'] = $request->file('image')->store('fund-transactions', 'public');
        }

        $fund->update($data);

        return back()->with('success', 'ແກ້ໄຂລາຍການສຳເລັດ');
    }

    public function destroy(FundTransaction $fund): RedirectResponse
    {
        if ($fund->image) {
            Storage::disk('public')->delete($fund->image);
        }
        $fund->delete();

        return back()->with('success', 'ລຶບລາຍການສຳເລັດ');
    }

    private function transformTransaction(FundTransaction $tx): array
    {
        return [
            'id' => $tx->id,
            'type' => $tx->type,
            'amount' => (float) $tx->amount,
            'transaction_date' => $tx->transaction_date->format('Y-m-d'),
            'transaction_date_month' => $tx->transaction_date->translatedFormat('M'),
            'transaction_date_day' => $tx->transaction_date->format('d'),
            'monk' => $tx->monk ? [
                'id' => $tx->monk->id,
                'full_name' => $tx->monk->full_name,
                'type_label' => $tx->monk->type_label,
                'photo_url' => $tx->monk->photo_url,
            ] : null,
            'party_name' => $tx->party_name,
            'party_label' => $tx->party_label,
            'description' => $tx->description,
            'image' => $tx->image,
            'image_url' => $tx->image_url,
            'recorded_by_name' => $tx->recordedBy?->name,
        ];
    }

    private function validateTransaction(Request $request): array
    {
        $validated = Validator::make($request->all(), [
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0',
            'transaction_date' => 'required|date',
            'source_type' => 'required|in:monk,other',
            'monk_id' => 'required_if:source_type,monk|nullable|exists:monks,id',
            'party_name' => 'required_if:source_type,other|nullable|string|max:200',
            'description' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:4096',
        ], [
            'type.required' => 'ກະລຸນາເລືອກປະເພດລາຍການ',
            'amount.required' => 'ກະລຸນາປ້ອນຈຳນວນເງິນ',
            'amount.numeric' => 'ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ',
            'transaction_date.required' => 'ກະລຸນາເລືອກວັນທີ',
            'monk_id.required_if' => 'ກະລຸນາເລືອກພຣະສົງ/ສາມະເນນ',
            'party_name.required_if' => 'ກະລຸນາປ້ອນຊື່ຜູ້ບໍລິຈາກ/ຜູ້ຮັບເງິນ',
            'image.image' => 'ໄຟລ໌ຕ້ອງເປັນຮູບພາບ',
        ])->validate();

        return [
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'transaction_date' => $validated['transaction_date'],
            'monk_id' => $validated['source_type'] === 'monk' ? $validated['monk_id'] : null,
            'party_name' => $validated['source_type'] === 'other' ? $validated['party_name'] : null,
            'description' => $validated['description'] ?: null,
        ];
    }
}
