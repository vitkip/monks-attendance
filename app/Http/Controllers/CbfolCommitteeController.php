<?php

namespace App\Http\Controllers;

use App\Models\CbfolCommittee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class CbfolCommitteeController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');
        $filterCategory = (string) $request->query('category', '');
        $filterTerm = (string) $request->query('term', '');
        $filterStatus = (string) $request->query('status', '');

        $committees = CbfolCommittee::query()
            ->search($search)
            ->byCategory($filterCategory)
            ->when($filterTerm, fn ($q) => $q->where('term', $filterTerm))
            ->when($filterStatus, fn ($q) => $q->where('status', $filterStatus))
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString()
            ->through(fn ($item) => [
                'id' => $item->id,
                'name' => $item->name,
                'title' => $item->title,
                'category' => $item->category,
                'category_label' => $item->category_label,
                'term' => $item->term,
                'term_year' => $item->term_year,
                'organization' => $item->organization,
                'phone' => $item->phone,
                'photo' => $item->photo,
                'photo_url' => $item->photo_url,
                'bio' => $item->bio,
                'sort_order' => $item->sort_order,
                'status' => $item->status,
                'status_label' => $item->status_label,
            ]);

        $categoryCounts = CbfolCommittee::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->pluck('count', 'category');

        $totalCount = (int) $categoryCounts->sum();

        return Inertia::render('CbfolCommittees/Index', [
            'filters' => [
                'search' => $search,
                'category' => $filterCategory,
                'term' => $filterTerm,
                'status' => $filterStatus,
            ],
            'committees' => $committees,
            'categories' => CbfolCommittee::categories(),
            'terms' => CbfolCommittee::terms(),
            'stats' => [
                'total' => $totalCount,
                'president' => (int) ($categoryCounts[CbfolCommittee::CATEGORY_PRESIDENT] ?? 0),
                'standing' => (int) ($categoryCounts[CbfolCommittee::CATEGORY_STANDING] ?? 0),
                'member' => (int) ($categoryCounts[CbfolCommittee::CATEGORY_MEMBER] ?? 0),
                'advisor' => (int) ($categoryCounts[CbfolCommittee::CATEGORY_ADVISOR] ?? 0),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateCommittee($request);
        $this->applyPhoto($request, $data);

        if (! isset($data['sort_order']) || $data['sort_order'] === null) {
            $data['sort_order'] = ((int) CbfolCommittee::where('category', $data['category'])->max('sort_order')) + 1;
        }

        CbfolCommittee::create($data);

        return back()->with('success', 'ເພີ່ມຂໍ້ມູນຄະນະກຳມະການສູນກາງ ອພສ ສຳເລັດ');
    }

    public function update(Request $request, CbfolCommittee $cbfolCommittee): RedirectResponse
    {
        $data = $this->validateCommittee($request, $cbfolCommittee->id);
        $this->applyPhoto($request, $data, $cbfolCommittee);

        $cbfolCommittee->update($data);

        return back()->with('success', 'ແກ້ໄຂຂໍ້ມູນສຳເລັດ');
    }

    public function destroy(CbfolCommittee $cbfolCommittee): RedirectResponse
    {
        if ($cbfolCommittee->photo && ! str_starts_with($cbfolCommittee->photo, 'http')) {
            Storage::disk('public')->delete($cbfolCommittee->photo);
        }

        $cbfolCommittee->delete();

        return back()->with('success', 'ລຶບຂໍ້ມູນສຳເລັດ');
    }

    private function validateCommittee(Request $request, ?int $id = null): array
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'title' => 'nullable|string|max:150',
            'category' => 'required|string|in:president,standing,member,advisor',
            'term' => 'required|string|max:50',
            'term_year' => 'nullable|string|max:50',
            'organization' => 'nullable|string|max:150',
            'phone' => 'nullable|string|max:50',
            'photo' => 'nullable',
            'photo_url_input' => 'nullable|string',
            'bio' => 'nullable|string|max:2000',
            'sort_order' => 'nullable|integer|min:0',
            'status' => 'required|string|in:active,inactive',
        ], [
            'name.required' => 'ກະລຸນາປ້ອນຊື່ ແລະ ສາຍາ/ນາມສະກຸນ',
            'category.required' => 'ກະລຸນາເລືອກໝວດໝູ່/ຕຳແໜ່ງ',
            'term.required' => 'ກະລຸນາເລືອກສະໄໝ',
        ]);

        return $validator->validate();
    }

    private function applyPhoto(Request $request, array &$data, ?CbfolCommittee $existing = null): void
    {
        if ($request->hasFile('photo')) {
            if ($existing && $existing->photo && ! str_starts_with($existing->photo, 'http')) {
                Storage::disk('public')->delete($existing->photo);
            }
            $data['photo'] = $request->file('photo')->store('cbfol-committees', 'public');
        } elseif ($request->file('photo') && ! $request->file('photo')->isValid()) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'photo' => 'ໄຟລ໌ຮູບພາບມີບັນຫາ ຫຼື ມີຂະໜາດໃຫຍ່ເກີນໄປ (ກະລຸນາເລືອກຮູບທີ່ມີຂະໜາດບໍ່ເກີນ 10MB)',
            ]);
        } elseif (! empty($request->input('photo_url_input'))) {
            $inputUrl = trim($request->input('photo_url_input'));

            // Handle Base64 Data URL if pasted directly
            if (str_starts_with($inputUrl, 'data:image/')) {
                if (preg_match('/^data:image\/(\w+);base64,(.+)$/', $inputUrl, $baseMatches)) {
                    $imageType = strtolower($baseMatches[1]);
                    $imageData = base64_decode($baseMatches[2]);
                    if ($imageData !== false) {
                        if ($existing && $existing->photo && ! str_starts_with($existing->photo, 'http')) {
                            Storage::disk('public')->delete($existing->photo);
                        }
                        $filename = 'cbfol-committees/' . \Illuminate\Support\Str::random(40) . '.' . ($imageType === 'jpeg' ? 'jpg' : $imageType);
                        Storage::disk('public')->put($filename, $imageData);
                        $data['photo'] = $filename;
                    }
                }
            } else {
                $data['photo'] = CbfolCommittee::formatPhotoUrl($inputUrl);
            }
        } elseif ($request->boolean('remove_photo')) {
            if ($existing && $existing->photo && ! str_starts_with($existing->photo, 'http')) {
                Storage::disk('public')->delete($existing->photo);
            }
            $data['photo'] = null;
        } else {
            unset($data['photo']);
        }

        unset($data['photo_url_input']);
        unset($data['remove_photo']);
    }
}
