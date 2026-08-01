<?php

namespace App\Http\Controllers;

use App\Models\ChantCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ChantCategoryController extends Controller
{
    public function index(): Response
    {
        $categories = ChantCategory::tree();

        return Inertia::render('ChantCategories/Index', [
            'categories' => $categories->map(fn (ChantCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'parent_id' => $category->parent_id,
                'depth' => $category->depth,
                'chants_count' => $category->chants_count,
                'descendant_ids' => $category->descendantIds(),
            ])->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCategory($request);

        ChantCategory::create([
            'name' => $validated['name'],
            'slug' => $this->generateSlug($validated['name']),
            'parent_id' => $validated['parent_id'] ?: null,
        ]);

        return back()->with('success', 'ເພີ່ມໝວດໝູ່ສຳເລັດ');
    }

    public function update(Request $request, ChantCategory $chantCategory): RedirectResponse
    {
        $validated = $this->validateCategory($request, $chantCategory);

        $chantCategory->update([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?: null,
        ]);

        return back()->with('success', 'ແກ້ໄຂໝວດໝູ່ສຳເລັດ');
    }

    public function destroy(ChantCategory $chantCategory): RedirectResponse
    {
        $chantCategory->loadCount('children');

        if ($chantCategory->children_count > 0) {
            return back()->with('error', 'ບໍ່ສາມາດລຶບໝວດໝູ່ທີ່ມີໝວດໝູ່ຍ່ອຍໄດ້ ກະລຸນາລຶບ ຫຼື ຍ້າຍໝວດໝູ່ຍ່ອຍກ່ອນ');
        }

        $chantCategory->delete();

        return back()->with('success', 'ລຶບໝວດໝູ່ສຳເລັດ');
    }

    private function validateCategory(Request $request, ?ChantCategory $category = null): array
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'parent_id' => [
                'nullable',
                'exists:chant_categories,id',
                function ($attribute, $value, $fail) use ($category) {
                    if ($value && $category && in_array((int) $value, $this->invalidParentIds($category), true)) {
                        $fail('ບໍ່ສາມາດເລືອກໝວດໝູ່ຍ່ອຍຂອງຕົນເອງເປັນໝວດໝູ່ແມ່ໄດ້');
                    }
                },
            ],
        ], [
            'name.required' => 'ກະລຸນາປ້ອນຊື່ໝວດໝູ່',
        ])->validate();
    }

    private function invalidParentIds(ChantCategory $category): array
    {
        return array_merge([$category->id], $category->descendantIds());
    }

    private function generateSlug(string $name): string
    {
        $base = Str::slug($name) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        while (ChantCategory::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
