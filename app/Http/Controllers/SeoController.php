<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\JobPost;
use App\Models\NewsPost;
use App\Models\Service;
use App\Models\TrainingCourse;
use Carbon\CarbonInterface;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    /**
     * Public pages with no database row: route name => [changefreq, priority].
     *
     * @var array<string, array{0: string, 1: string}>
     */
    private const STATIC_PAGES = [
        'home' => ['weekly', '1.0'],
        'about' => ['monthly', '0.6'],
        'services' => ['monthly', '0.8'],
        'training' => ['monthly', '0.8'],
        'jobs' => ['daily', '0.9'],
        'countries' => ['monthly', '0.8'],
        'gallery' => ['monthly', '0.5'],
        'success-stories' => ['monthly', '0.5'],
        'news' => ['weekly', '0.6'],
        'apply' => ['monthly', '0.7'],
        'contact' => ['monthly', '0.7'],
        'terms' => ['yearly', '0.2'],
        'privacy-policy' => ['yearly', '0.2'],
    ];

    public function sitemap(): Response
    {
        $urls = [];

        foreach (self::STATIC_PAGES as $name => [$freq, $priority]) {
            $urls[] = [route($name), null, $freq, $priority];
        }

        foreach (Service::active()->ordered()->get(['slug', 'updated_at']) as $item) {
            $urls[] = [route('services.show', $item->slug), $item->updated_at, 'monthly', '0.7'];
        }

        foreach (Country::active()->ordered()->get(['slug', 'updated_at']) as $item) {
            $urls[] = [route('countries.show', $item->slug), $item->updated_at, 'monthly', '0.7'];
        }

        foreach (TrainingCourse::active()->ordered()->get(['slug', 'updated_at']) as $item) {
            $urls[] = [route('training.show', $item->slug), $item->updated_at, 'monthly', '0.7'];
        }

        foreach (JobPost::open()->latest('updated_at')->get(['slug', 'updated_at']) as $item) {
            $urls[] = [route('jobs.show', $item->slug), $item->updated_at, 'daily', '0.8'];
        }

        foreach (NewsPost::published()->latest('published_at')->get(['slug', 'updated_at']) as $item) {
            $urls[] = [route('news.show', $item->slug), $item->updated_at, 'monthly', '0.5'];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

        foreach ($urls as [$loc, $lastmod, $freq, $priority]) {
            $xml .= "  <url>\n    <loc>".e($loc)."</loc>\n";

            if ($lastmod instanceof CarbonInterface) {
                $xml .= '    <lastmod>'.$lastmod->toAtomString()."</lastmod>\n";
            }

            $xml .= "    <changefreq>{$freq}</changefreq>\n    <priority>{$priority}</priority>\n  </url>\n";
        }

        $xml .= '</urlset>'."\n";

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    public function robots(): Response
    {
        // Only the live site may be indexed; staging and local stay hidden.
        if (! app()->environment('production')) {
            $body = "User-agent: *\nDisallow: /\n";
        } else {
            $body = "User-agent: *\nDisallow: /admin\n\nSitemap: ".route('sitemap')."\n";
        }

        return response($body, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
