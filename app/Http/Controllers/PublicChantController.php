<?php

namespace App\Http\Controllers;

use App\Models\Chant;
use App\Models\ChantCategory;
use Illuminate\Http\Request;

class PublicChantController extends Controller
{
    public function index(Request $request)
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
            ->withQueryString();

        $categories = ChantCategory::tree();

        return view('public.chants.index', compact('chants', 'categories', 'categorySlug'));
    }

    public function show(string $slug)
    {
        $chant = Chant::with('category')->where('slug', $slug)->firstOrFail();

        $related = Chant::where('id', '!=', $chant->id)
            ->when($chant->category_id, fn($q) => $q->where('category_id', $chant->category_id))
            ->orderBy('title')
            ->limit(3)
            ->get();

        return view('public.chants.show', compact('chant', 'related'));
    }
}
