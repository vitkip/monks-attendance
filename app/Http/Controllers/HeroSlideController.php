<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;
use Inertia\Response;

class HeroSlideController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('HeroSlides/Index', [
            'slides' => HeroSlide::ordered()->get()->map(fn (HeroSlide $slide) => [
                'id' => $slide->id,
                'title' => $slide->title,
                'subtitle' => $slide->subtitle,
                'link_url' => $slide->link_url,
                'button_text' => $slide->button_text,
                'image_url' => $slide->image_url,
                'status' => (bool) $slide->status,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateSlide($request, isEdit: false);
        $data['image'] = $request->file('image')->store('hero-slides', 'public');
        $data['sort_order'] = (int) HeroSlide::max('sort_order') + 1;

        HeroSlide::create($data);

        return back()->with('success', 'ເພີ່ມສະໄລ້ສຳເລັດ');
    }

    public function update(Request $request, HeroSlide $heroSlide): RedirectResponse
    {
        $data = $this->validateSlide($request, isEdit: true);

        if ($request->hasFile('image')) {
            if ($heroSlide->image) {
                Storage::disk('public')->delete($heroSlide->image);
            }
            $data['image'] = $request->file('image')->store('hero-slides', 'public');
        }

        $heroSlide->update($data);

        return back()->with('success', 'ແກ້ໄຂສະໄລ້ສຳເລັດ');
    }

    public function togglePublish(HeroSlide $heroSlide): RedirectResponse
    {
        $heroSlide->status = ! $heroSlide->status;
        $heroSlide->save();

        return back()->with('success', $heroSlide->status ? 'ເຜີຍແຜ່ສະໄລ້ແລ້ວ' : 'ເກັບເປັນຮ່າງແລ້ວ');
    }

    public function moveUp(HeroSlide $heroSlide): RedirectResponse
    {
        $this->swap($heroSlide, -1);

        return back();
    }

    public function moveDown(HeroSlide $heroSlide): RedirectResponse
    {
        $this->swap($heroSlide, 1);

        return back();
    }

    public function destroy(HeroSlide $heroSlide): RedirectResponse
    {
        if ($heroSlide->image) {
            Storage::disk('public')->delete($heroSlide->image);
        }
        $heroSlide->delete();

        return back()->with('success', 'ລຶບສະໄລ້ສຳເລັດ');
    }

    private function swap(HeroSlide $slide, int $direction): void
    {
        $slides = HeroSlide::ordered()->get();
        $index = $slides->search(fn ($s) => $s->id === $slide->id);

        if ($index === false) {
            return;
        }

        $targetIndex = $index + $direction;

        if ($targetIndex < 0 || $targetIndex >= $slides->count()) {
            return;
        }

        $current = $slides[$index];
        $target = $slides[$targetIndex];

        [$currentOrder, $targetOrder] = [$current->sort_order, $target->sort_order];
        $current->update(['sort_order' => $targetOrder]);
        $target->update(['sort_order' => $currentOrder]);
    }

    private function validateSlide(Request $request, bool $isEdit): array
    {
        $validated = Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'subtitle' => 'nullable|string|max:500',
            'link_url' => 'nullable|url|max:255',
            'button_text' => 'nullable|string|max:50',
            'image' => $isEdit ? 'nullable|image|max:2048' : 'required|image|max:2048',
            'status' => 'boolean',
        ], [
            'title.required' => 'ກະລຸນາປ້ອນຫົວຂໍ້ສະໄລ້',
            'image.required' => 'ກະລຸນາເລືອກຮູບພາບສະໄລ້',
            'link_url.url' => 'ກະລຸນາປ້ອນລິ້ງທີ່ຖືກຕ້ອງ (ຕ້ອງຂຶ້ນຕົ້ນດ້ວຍ http:// ຫຼື https://)',
        ])->validate();

        return [
            'title' => $validated['title'],
            'subtitle' => $validated['subtitle'] ?: null,
            'link_url' => $validated['link_url'] ?: null,
            'button_text' => $validated['button_text'] ?: null,
            'status' => $request->boolean('status'),
        ];
    }
}
