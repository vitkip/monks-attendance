<?php

namespace App\Http\Controllers;

use App\Models\Chant;
use App\Models\ChantCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PublicChantController extends Controller
{
    public function index(Request $request): Response
    {
        $categorySlug = $request->query('category');
        $category = $categorySlug ? ChantCategory::where('slug', $categorySlug)->first() : null;

        $chants = Chant::query()
            ->when($category, function ($q) use ($category) {
                $ids = array_merge([$category->id], $category->descendantIds());
                $q->whereIn('category_id', $ids);
            })
            ->with('category')
            ->orderBy('title')
            ->paginate(12)
            ->withQueryString()
            ->through(fn (Chant $chant) => [
                'id' => $chant->id,
                'title' => $chant->title,
                'slug' => $chant->slug,
                'excerpt' => Str::limit(strip_tags($chant->content), 120),
                'minutes' => max(1, (int) ceil(Str::wordCount(strip_tags($chant->content)) / 150)),
                'category' => $chant->category ? [
                    'name' => $chant->category->name,
                    'slug' => $chant->category->slug,
                ] : null,
            ]);

        $categories = ChantCategory::tree()->map(fn (ChantCategory $category) => [
            'name' => $category->name,
            'slug' => $category->slug,
            'depth' => $category->depth,
        ])->values();

        return Inertia::render('Public/Chants/Index', [
            'chants' => $chants,
            'categories' => $categories,
            'categorySlug' => $categorySlug,
        ]);
    }

    public function show(string $slug): Response
    {
        $chant = Chant::with('category')->where('slug', $slug)->firstOrFail();

        $related = Chant::where('id', '!=', $chant->id)
            ->when($chant->category_id, fn ($q) => $q->where('category_id', $chant->category_id))
            ->orderBy('title')
            ->limit(3)
            ->get()
            ->map(fn (Chant $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
            ])
            ->values();

        return Inertia::render('Public/Chants/Show', [
            'chant' => [
                'id' => $chant->id,
                'title' => $chant->title,
                'slug' => $chant->slug,
                'content_html' => $chant->content_html,
                'minutes' => max(1, (int) ceil(Str::wordCount(strip_tags($chant->content)) / 150)),
                'category' => $chant->category ? [
                    'name' => $chant->category->name,
                    'slug' => $chant->category->slug,
                ] : null,
            ],
            'related' => $related,
        ]);
    }
}
