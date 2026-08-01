<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class NewsController extends Controller
{
    public function index(Request $request): Response
    {
        $isAdmin = $request->user()->isAdmin();

        $search = (string) $request->query('search', '');
        $filterStatus = (string) $request->query('status', '');
        $filterCategory = (string) $request->query('category', '');

        $news = News::query()
            ->when(! $isAdmin, fn ($q) => $q->published())
            ->when($isAdmin && $filterStatus !== '', fn ($q) => $q->where('status', $filterStatus))
            ->when($filterCategory !== '', fn ($q) => $q->where('category_id', $filterCategory))
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%")
                ->orWhere('excerpt', 'like', "%{$search}%")
            )
            ->with(['author', 'category'])
            ->latest('published_at')
            ->latest()
            ->paginate(9)
            ->withQueryString()
            ->through(fn (News $item) => [
                'id' => $item->id,
                'title' => $item->title,
                'excerpt' => $item->excerpt,
                'excerpt_or_summary' => $item->excerpt_or_summary,
                'content' => $item->content_html,
                'image' => $item->image,
                'image_url' => $item->image_url,
                'status' => (bool) $item->status,
                'category_id' => $item->category_id,
                'category' => $item->category ? [
                    'id' => $item->category->id,
                    'name' => $item->category->name,
                ] : null,
                'author_name' => $item->author?->name,
                'date_label' => ($item->published_at ?? $item->created_at)->format('d/m/Y'),
                'created_at' => $item->created_at->toISOString(),
            ]);

        return Inertia::render('News/Index', [
            'filters' => [
                'search' => $search,
                'status' => $filterStatus,
                'category' => $filterCategory,
            ],
            'news' => $news,
            'categories' => NewsCategory::orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateNews($request);

        $data = [
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'] ?: null,
            'content' => clean($validated['content'], 'editor'),
            'status' => $request->boolean('publishNow'),
            'category_id' => $validated['category_id'] ?: null,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $data['slug'] = $this->generateSlug($validated['title']);
        $data['user_id'] = auth()->id();
        $data['published_at'] = $data['status'] ? now() : null;

        News::create($data);

        return back()->with('success', 'ເພີ່ມຂ່າວສຳເລັດ');
    }

    public function update(Request $request, News $news): RedirectResponse
    {
        $validated = $this->validateNews($request);

        $publishNow = $request->boolean('publishNow');
        $removeExistingImage = $request->boolean('removeExistingImage');

        $data = [
            'title' => $validated['title'],
            'excerpt' => $validated['excerpt'] ?: null,
            'content' => clean($validated['content'], 'editor'),
            'status' => $publishNow,
            'category_id' => $validated['category_id'] ?: null,
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        } elseif ($removeExistingImage) {
            $data['image'] = null;
        }

        if (($request->hasFile('image') || $removeExistingImage) && $news->image) {
            Storage::disk('public')->delete($news->image);
        }

        if ($publishNow && ! $news->status) {
            $data['published_at'] = now();
        }

        $news->update($data);

        return back()->with('success', 'ແກ້ໄຂຂ່າວສຳເລັດ');
    }

    public function destroy(News $news): RedirectResponse
    {
        $news->delete();

        return back()->with('success', 'ລຶບຂ່າວສຳເລັດ');
    }

    public function togglePublish(News $news): RedirectResponse
    {
        $news->status = ! $news->status;

        if ($news->status && ! $news->published_at) {
            $news->published_at = now();
        }

        $news->save();

        return back()->with('success', $news->status ? 'ເຜີຍແຜ່ຂ່າວແລ້ວ' : 'ເກັບເປັນຮ່າງແລ້ວ');
    }

    private function validateNews(Request $request): array
    {
        return Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'excerpt' => 'nullable|string|max:500',
            'content' => ['required', 'string', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('ກະລຸນາປ້ອນເນື້ອຫາຂ່າວ');
                }
            }],
            'image' => 'nullable|image|max:2048',
            'category_id' => 'nullable|exists:news_categories,id',
        ], [
            'title.required' => 'ກະລຸນາປ້ອນຫົວຂໍ້ຂ່າວ',
            'content.required' => 'ກະລຸນາປ້ອນເນື້ອຫາຂ່າວ',
        ])->validate();
    }

    private function generateSlug(string $title): string
    {
        $base = Str::slug($title) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        while (News::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
