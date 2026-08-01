<?php

namespace App\Http\Controllers;

use App\Models\NewsCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class NewsCategoryController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('NewsCategories/Index', [
            'categories' => NewsCategory::withCount('news')->orderBy('name')->get()->map(fn ($category) => [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'news_count' => $category->news_count,
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateCategory($request);

        NewsCategory::create([
            'name' => $validated['name'],
            'slug' => $this->generateSlug($validated['name']),
        ]);

        return back()->with('success', 'ເພີ່ມໝວດໝູ່ສຳເລັດ');
    }

    public function update(Request $request, NewsCategory $newsCategory): RedirectResponse
    {
        $validated = $this->validateCategory($request);

        $newsCategory->update(['name' => $validated['name']]);

        return back()->with('success', 'ແກ້ໄຂໝວດໝູ່ສຳເລັດ');
    }

    public function destroy(NewsCategory $newsCategory): RedirectResponse
    {
        $newsCategory->delete();

        return back()->with('success', 'ລຶບໝວດໝູ່ສຳເລັດ');
    }

    private function validateCategory(Request $request): array
    {
        return Validator::make($request->all(), [
            'name' => 'required|string|max:100',
        ], [
            'name.required' => 'ກະລຸນາປ້ອນຊື່ໝວດໝູ່',
        ])->validate();
    }

    private function generateSlug(string $name): string
    {
        $base = Str::slug($name) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        while (NewsCategory::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
