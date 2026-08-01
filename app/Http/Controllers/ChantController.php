<?php

namespace App\Http\Controllers;

use App\Models\Chant;
use App\Models\ChantCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ChantController extends Controller
{
    public function index(Request $request): Response
    {
        $search = (string) $request->query('search', '');
        $filterCategory = (string) $request->query('category', '');

        $chants = Chant::query()
            ->when($filterCategory !== '', function ($q) use ($filterCategory) {
                $category = ChantCategory::find($filterCategory);
                $ids = $category ? array_merge([$category->id], $category->descendantIds()) : [$filterCategory];
                $q->whereIn('category_id', $ids);
            })
            ->when($search, fn ($q) => $q->where('title', 'like', "%{$search}%"))
            ->with('category')
            ->orderBy('title')
            ->paginate(9)
            ->withQueryString()
            ->through(fn (Chant $chant) => [
                'id' => $chant->id,
                'title' => $chant->title,
                'slug' => $chant->slug,
                'content_html' => $chant->content_html,
                'category_id' => $chant->category_id,
                'category' => $chant->category ? [
                    'id' => $chant->category->id,
                    'name' => $chant->category->name,
                ] : null,
            ]);

        return Inertia::render('Chants/Index', [
            'filters' => [
                'search' => $search,
                'category' => $filterCategory,
            ],
            'chants' => $chants,
            'categories' => ChantCategory::tree()->map(fn (ChantCategory $category) => [
                'id' => $category->id,
                'name' => $category->name,
                'depth' => $category->depth,
            ])->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $this->validateChant($request);

        Chant::create([
            'title' => $validated['title'],
            'content' => clean($validated['content'], 'editor'),
            'category_id' => $validated['category_id'] ?: null,
            'slug' => $this->generateSlug($validated['title']),
        ]);

        return back()->with('success', 'ເພີ່ມບົດສູດມົນສຳເລັດ');
    }

    public function update(Request $request, Chant $chant): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $validated = $this->validateChant($request);

        $chant->update([
            'title' => $validated['title'],
            'content' => clean($validated['content'], 'editor'),
            'category_id' => $validated['category_id'] ?: null,
        ]);

        return back()->with('success', 'ແກ້ໄຂບົດສູດມົນສຳເລັດ');
    }

    public function destroy(Chant $chant): RedirectResponse
    {
        abort_unless(auth()->user()->isAdmin(), 403);

        $chant->delete();

        return back()->with('success', 'ລຶບບົດສູດມົນສຳເລັດ');
    }

    private function validateChant(Request $request): array
    {
        return Validator::make($request->all(), [
            'title' => 'required|string|max:200',
            'content' => ['required', 'string', function ($attribute, $value, $fail) {
                if (trim(strip_tags($value)) === '') {
                    $fail('ກະລຸນາປ້ອນເນື້ອຫາບົດສູດມົນ');
                }
            }],
            'category_id' => 'nullable|exists:chant_categories,id',
        ], [
            'title.required' => 'ກະລຸນາປ້ອນຫົວຂໍ້ບົດສູດມົນ',
            'content.required' => 'ກະລຸນາປ້ອນເນື້ອຫາບົດສູດມົນ',
        ])->validate();
    }

    private function generateSlug(string $title): string
    {
        $base = Str::slug($title) ?: Str::random(8);
        $slug = $base;
        $i = 1;

        while (Chant::where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
