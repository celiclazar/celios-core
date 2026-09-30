<?php

namespace Celios\Core\Http\Controllers;

use Celios\Core\Services\Sitemap\SitemapGenerator;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Render the XML sitemap.
     */
    public function index(SitemapGenerator $generator): Response
    {
        $xml = $generator->generateXml();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8')
            ->header('Cache-Control', 'public, max-age=3600');
    }

    /**
     * Render dynamic robots.txt pointing to the sitemap.
     */
    public function robots(): Response
    {
        $sitemapUrl = url('/sitemap.xml');
        $excluded = config('sitemap.excluded_patterns', ['/admin*', '/profile*']);

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";

        foreach ($excluded as $pattern) {
            $cleaned = '/' . ltrim(rtrim($pattern, '*'), '/');
            $content .= "Disallow: {$cleaned}\n";
        }

        $content .= "\nSitemap: {$sitemapUrl}\n";

        return response($content, 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
