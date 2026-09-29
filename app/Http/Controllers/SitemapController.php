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

        $xml = \Illuminate\Support\Facades\Cache::remember('sitemap:xml', 43200, function () use ($urls) {
            // Add published news articles (only select required metadata columns)
            $newsList = News::published()->select(['slug', 'updated_at', 'published_at'])->latest('updated_at')->get();
            foreach ($newsList as $item) {
                $urls[] = [
                    'loc' => route('news.public.show', $item->slug),
                    'lastmod' => ($item->updated_at ?? $item->published_at ?? now())->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            }

            // Add chants
            $chants = Chant::select(['slug', 'updated_at'])->latest('updated_at')->get();
            foreach ($chants as $chant) {
                $urls[] = [
                    'loc' => route('chants.public.show', $chant->slug),
                    'lastmod' => ($chant->updated_at ?? now())->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            }

            // Add construction projects
            $projects = ConstructionProject::select(['id', 'updated_at'])->latest('updated_at')->get();
            foreach ($projects as $project) {
                $urls[] = [
                    'loc' => route('construction-projects.public.show', $project->id),
                    'lastmod' => ($project->updated_at ?? now())->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.8',
                ];
            }

            return view('sitemap', compact('urls'))->render();
        });

        return response($xml, 200)
            ->header('Content-Type', 'text/xml')
            ->header('Cache-Control', 'public, max-age=43200');
    }
}
