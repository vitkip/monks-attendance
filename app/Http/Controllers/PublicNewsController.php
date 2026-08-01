<?php

namespace App\Http\Controllers;

use App\Models\HeroSlide;
use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PublicNewsController extends Controller
{
    /**
     * Home page for guests; authenticated users are redirected to their dashboard.
     */
    public function index(Request $request): Response|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('absences.index');
        }

        $categorySlug = $request->query('category');

        $heroSlides = HeroSlide::active()->ordered()->get()->map(fn (HeroSlide $slide) => [
            'id' => $slide->id,
            'title' => $slide->title,
            'subtitle' => $slide->subtitle,
            'image_url' => $slide->image_url,
            'link_url' => $slide->link_url,
            'button_text' => $slide->button_text,
        ]);

        $featured = (!$categorySlug && $heroSlides->isEmpty())
            ? News::published()->with(['author', 'category'])->latest('published_at')->latest()->first()
            : null;

        $news = News::published()
            ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
            ->when($categorySlug, fn ($q) =>
                $q->whereHas('category', fn ($c) => $c->where('slug', $categorySlug))
            )
            ->with(['author', 'category'])
            ->latest('published_at')
            ->latest()
            ->paginate(9)
            ->withQueryString()
            ->through(fn (News $item) => $this->transformCard($item));

        $categories = NewsCategory::orderBy('name')->get(['id', 'name', 'slug']);

        $latestNews = News::published()
            ->when($featured, fn ($q) => $q->where('id', '!=', $featured->id))
            ->with('category')
            ->latest('published_at')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function (News $item) {
                $date = $item->published_at ?? $item->created_at;

                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'category' => $item->category ? ['name' => $item->category->name] : null,
                    'month_label' => $date->translatedFormat('M'),
                    'day_label' => $date->format('d'),
                ];
            });

        return Inertia::render('Public/News/Index', [
            'heroSlides' => $heroSlides,
            'featured' => $featured ? $this->transformCard($featured) : null,
            'news' => $news,
            'categories' => $categories,
            'categorySlug' => $categorySlug,
            'latestNews' => $latestNews,
        ]);
    }

    public function show(string $slug): Response
    {
        $article = News::published()->with(['author', 'category'])->where('slug', $slug)->firstOrFail();

        $recent = News::published()
            ->where('id', '!=', $article->id)
            ->with(['author', 'category'])
            ->latest('published_at')
            ->latest()
            ->limit(3)
            ->get()
            ->map(fn (News $item) => $this->transformCard($item));

        $date = $article->published_at ?? $article->created_at;

        return Inertia::render('Public/News/Show', [
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt_or_summary' => $article->excerpt_or_summary,
                'content' => $article->content_html,
                'image_url' => $article->image_url,
                'category' => $article->category ? ['name' => $article->category->name] : null,
                'author_name' => $article->author?->name,
                'date_label' => $date->format('d/m/Y'),
                'date_iso' => $date->toDateString(),
                'minutes_label' => $this->readingMinutes($article->content),
            ],
            'recent' => $recent,
        ]);
    }

    /**
     * Shape a News row into the card payload shared by the featured banner,
     * the main grid, and the "related articles" strip on the show page.
     */
    private function transformCard(News $item): array
    {
        $date = $item->published_at ?? $item->created_at;

        return [
            'id' => $item->id,
            'title' => $item->title,
            'slug' => $item->slug,
            'excerpt_or_summary' => $item->excerpt_or_summary,
            'image_url' => $item->image_url,
            'category' => $item->category ? [
                'name' => $item->category->name,
                'slug' => $item->category->slug,
            ] : null,
            'author_name' => $item->author?->name,
            'date_label' => $date->format('d/m/Y'),
            'date_iso' => $date->toDateString(),
            'minutes_label' => $this->readingMinutes($item->content),
        ];
    }

    private function readingMinutes(?string $content): int
    {
        return max(1, (int) ceil(Str::wordCount(strip_tags((string) $content)) / 200));
    }
}
