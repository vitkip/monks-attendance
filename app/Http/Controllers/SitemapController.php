<?php

namespace App\Http\Controllers;

use App\Models\Chant;
use App\Models\ConstructionProject;
use App\Models\News;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Generate dynamic sitemap.xml for SEO indexing.
     */
    public function index(): Response
    {
        $urls = [
            [
                'loc' => route('news.public.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ],
            [
                'loc' => route('monks.public.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('chants.public.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('construction-projects.public.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('electricity-bills.public.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('fund.public.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ],
            [
                'loc' => route('absences.public.index'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '0.6',
            ],
        ];

        // Add published news articles
        $newsList = News::published()->latest('updated_at')->get();
        foreach ($newsList as $item) {
            $urls[] = [
                'loc' => route('news.public.show', $item->slug),
                'lastmod' => ($item->updated_at ?? $item->published_at ?? now())->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];
        }

        // Add chants
        $chants = Chant::latest('updated_at')->get();
        foreach ($chants as $chant) {
            $urls[] = [
                'loc' => route('chants.public.show', $chant->slug),
                'lastmod' => ($chant->updated_at ?? now())->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];
        }

        // Add construction projects
        $projects = ConstructionProject::latest('updated_at')->get();
        foreach ($projects as $project) {
            $urls[] = [
                'loc' => route('construction-projects.public.show', $project->id),
                'lastmod' => ($project->updated_at ?? now())->toAtomString(),
                'changefreq' => 'monthly',
                'priority' => '0.8',
            ];
        }

        $xml = view('sitemap', compact('urls'))->render();

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
