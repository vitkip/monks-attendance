<?php

namespace App\Http\Controllers;

use App\Models\CbfolCommittee;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicCbfolCommitteeController extends Controller
{
    public function index(Request $request): Response
    {
        $selectedCategory = (string) $request->query('category', '');
        $search = (string) $request->query('search', '');
        $selectedTerm = (string) $request->query('term', 'ສະໄໝທີ IX');

        $query = CbfolCommittee::query()
            ->active()
            ->when($selectedTerm, fn ($q) => $q->where('term', $selectedTerm))
            ->search($search)
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc');

        $allMembers = (clone $query)->get();

        $mapMember = fn (CbfolCommittee $item) => [
            'id' => $item->id,
            'name' => $item->name,
            'title' => $item->title,
            'category' => $item->category,
            'category_label' => $item->category_label,
            'term' => $item->term,
            'term_year' => $item->term_year,
            'organization' => $item->organization,
            'photo_url' => $item->photo_url,
            'phone' => $item->phone,
            'bio' => $item->bio,
            'sort_order' => $item->sort_order,
        ];

        // Grouped members
        $presidents = $allMembers->where('category', CbfolCommittee::CATEGORY_PRESIDENT)->values()->map($mapMember)->all();
        $standing = $allMembers->where('category', CbfolCommittee::CATEGORY_STANDING)->values()->map($mapMember)->all();
        $committeeMembers = $allMembers->where('category', CbfolCommittee::CATEGORY_MEMBER)->values()->map($mapMember)->all();
        $advisors = $allMembers->where('category', CbfolCommittee::CATEGORY_ADVISOR)->values()->map($mapMember)->all();

        // Total counts for active term
        $baseCountQuery = CbfolCommittee::active()->where('term', $selectedTerm);
        $counts = [
            'total' => (int) (clone $baseCountQuery)->count(),
            'president' => (int) (clone $baseCountQuery)->where('category', CbfolCommittee::CATEGORY_PRESIDENT)->count(),
            'standing' => (int) (clone $baseCountQuery)->where('category', CbfolCommittee::CATEGORY_STANDING)->count(),
            'member' => (int) (clone $baseCountQuery)->where('category', CbfolCommittee::CATEGORY_MEMBER)->count(),
            'advisor' => (int) (clone $baseCountQuery)->where('category', CbfolCommittee::CATEGORY_ADVISOR)->count(),
        ];

        return Inertia::render('Public/CbfolCommittees/Index', [
            'presidents' => $presidents,
            'standing' => $standing,
            'committeeMembers' => $committeeMembers,
            'advisors' => $advisors,
            'counts' => $counts,
            'categories' => CbfolCommittee::categories(),
            'selectedCategory' => $selectedCategory,
            'selectedTerm' => $selectedTerm,
            'search' => $search,
        ]);
    }
}
