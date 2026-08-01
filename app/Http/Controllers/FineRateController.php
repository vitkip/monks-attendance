<?php

namespace App\Http\Controllers;

use App\Models\FineRate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class FineRateController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('FineRates/Index', [
            'rates' => FineRate::withCount('absences')->latest()->get()->map(fn ($rate) => [
                'id' => $rate->id,
                'name' => $rate->name,
                'amount' => (float) $rate->amount,
                'description' => $rate->description,
                'absences_count' => $rate->absences_count,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        FineRate::create($this->validateRate($request));

        return back()->with('success', 'ເພີ່ມຂໍ້ມູນສຳເລັດ');
    }

    public function update(Request $request, FineRate $fineRate): RedirectResponse
    {
        $fineRate->update($this->validateRate($request));

        return back()->with('success', 'ແກ້ໄຂຂໍ້ມູນສຳເລັດ');
    }

    public function destroy(FineRate $fineRate): RedirectResponse
    {
        $fineRate->delete();

        return back()->with('success', 'ລຶບຂໍ້ມູນສຳເລັດ');
    }

    private function validateRate(Request $request): array
    {
        $validated = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
        ], [
            'name.required' => 'ກະລຸນາປ້ອນຊື່ປະເພດ',
            'amount.required' => 'ກະລຸນາປ້ອນຈຳນວນເງິນ',
            'amount.numeric' => 'ຈຳນວນເງິນຕ້ອງເປັນຕົວເລກ',
        ])->validate();

        $validated['description'] = $validated['description'] ?: null;

        return $validated;
    }
}
