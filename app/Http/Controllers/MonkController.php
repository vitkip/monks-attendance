<?php

namespace App\Http\Controllers;

use App\Models\Monk;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator as ValidatorInstance;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class MonkController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');
        $filterType = (string) $request->query('type', '');
        $filterStatus = (string) $request->query('status', '');

        $monks = Monk::query()
            ->when($search, fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('surname', 'like', "%{$search}%")
                ->orWhere('temple', 'like', "%{$search}%")
            )
            ->when($filterType, fn ($q) => $q->where('type', $filterType))
            ->when($filterStatus, fn ($q) => $q->where('status', $filterStatus))
            ->withCount('absences')
            ->withSum('absences', 'fine_amount')
            ->latest()
            ->paginate(10)
            ->withQueryString()
            ->through(fn ($monk) => [
                'id' => $monk->id,
                'name' => $monk->name,
                'surname' => $monk->surname,
                'full_name' => $monk->full_name,
                'type' => $monk->type,
                'type_label' => $monk->type_label,
                'status' => $monk->status,
                'status_label' => $monk->status_label,
                'photo_url' => $monk->photo_url,
                'temple' => $monk->temple,
                'pansa' => $monk->pansa,
                'age' => $monk->age,
                'birth_date' => $monk->birth_date?->format('Y-m-d'),
                'ordination_date' => $monk->ordination_date?->format('Y-m-d'),
                'absences_count' => $monk->absences_count,
                'absences_sum_fine_amount' => (float) ($monk->absences_sum_fine_amount ?? 0),
            ]);

        return Inertia::render('Monks/Index', [
            'filters' => ['search' => $search, 'type' => $filterType, 'status' => $filterStatus],
            'monks' => $monks,
            'statuses' => Monk::statuses(),
            'totalCount' => Monk::where('status', 'active')->count(),
            'monkCount' => Monk::where('status', 'active')->where('type', 'monk')->count(),
            'noviceCount' => Monk::where('status', 'active')->where('type', 'novice')->count(),
            'nunCount' => Monk::where('status', 'active')->where('type', 'nun')->count(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateMonk($request);
        $this->applyPhoto($request, $data);

        Monk::create($data);

        return back()->with('success', 'ເພີ່ມຂໍ້ມູນສຳເລັດ');
    }

    public function update(Request $request, Monk $monk): RedirectResponse
    {
        $data = $this->validateMonk($request);
        $this->applyPhoto($request, $data);

        $monk->update($data);

        return back()->with('success', 'ແກ້ໄຂຂໍ້ມູນສຳເລັດ');
    }

    public function destroy(Monk $monk): RedirectResponse
    {
        $monk->delete();

        return back()->with('success', 'ລຶບຂໍ້ມູນສຳເລັດ');
    }

    private function validateMonk(Request $request): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'surname' => 'required|string|max:100',
            'type' => ['required', Rule::in(['monk', 'novice', 'nun'])],
            'status' => ['required', Rule::in(array_keys(Monk::statuses()))],
            'birth_date' => 'nullable|date|before:today',
            'ordination_date' => 'nullable|date|before_or_equal:today',
            'temple' => 'nullable|string|max:150',
            'photo' => 'nullable|image|max:2048',
        ], [
            'name.required' => 'ກະລຸນາປ້ອນຊື່',
            'surname.required' => 'ກະລຸນາປ້ອນນາມສະກຸນ',
            'birth_date.before' => 'ວັນເດືອນປີເກີດຕ້ອງເປັນວັນທີ່ຜ່ານມາແລ້ວ',
            'ordination_date.before_or_equal' => 'ວັນເດືອນປີບວດຕ້ອງບໍ່ຢູ່ໃນອະນາຄົດ',
        ]);

        $validator->after(function (ValidatorInstance $validator) use ($request) {
            $birthDate = $request->input('birth_date');
            $ordinationDate = $request->input('ordination_date');

            if (! $birthDate || ! $ordinationDate) {
                return;
            }

            $birth = Carbon::parse($birthDate);
            $ordination = Carbon::parse($ordinationDate);

            if ($ordination->lt($birth)) {
                $validator->errors()->add('ordination_date', 'ວັນບວດຕ້ອງບໍ່ຢູ່ກ່ອນວັນເກີດ');

                return;
            }

            if ($request->input('type') === 'monk' && $birth->diffInYears($ordination) < 20) {
                $validator->errors()->add('ordination_date', 'ພຣະສົງຕ້ອງມີອາຍຸຢ່າງໜ້ອຍ 20 ປີຈຶ່ງຈະອຸປະສົມບົດເປັນພຣະໄດ້');
            }
        });

        $validated = $validator->validate();

        return [
            'name' => $validated['name'],
            'surname' => $validated['surname'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'birth_date' => $validated['birth_date'] ?: null,
            'ordination_date' => $validated['ordination_date'] ?: null,
            'pansa' => Monk::calculatePansa($validated['ordination_date'] ?? null),
            'temple' => $validated['temple'] ?: null,
        ];
    }

    private function applyPhoto(Request $request, array &$data): void
    {
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('monks', 'public');
        }
    }
}
