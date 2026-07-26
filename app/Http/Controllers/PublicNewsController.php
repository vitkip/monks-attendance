<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class PublicNewsController extends Controller
{
    public function index(Request $request)
    {
        $categorySlug = $request->query('category');

        $heroSlides = HeroSlide::active()->ordered()->get();

        $featured = $categorySlug ? null : News::published()->latest('published_at')->latest()->first();

        $news = News::published()
            ->when($featured, fn($q) => $q->where('id', '!=', $featured->id))
            ->when($categorySlug, fn($q) =>
                $q->whereHas('category', fn($c) => $c->where('slug', $categorySlug))
            )
            ->with(['author', 'category'])
            ->latest('published_at')
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $categories = NewsCategory::orderBy('name')->get();

        $latestNews = News::published()
            ->when($featured, fn($q) => $q->where('id', '!=', $featured->id))
            ->with('category')
            ->latest('published_at')
            ->latest()
            ->limit(5)
            ->get();

        return view('public.news.index', compact('heroSlides', 'featured', 'news', 'categories', 'categorySlug', 'latestNews'));
    }

    public function show(string $slug)
    {
        $article = News::published()->with(['author', 'category'])->where('slug', $slug)->firstOrFail();

        $recent = News::published()
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->latest()
            ->limit(3)
            ->get();

        return view('public.news.show', compact('article', 'recent'));
    }
}
