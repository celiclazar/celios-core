<?php

namespace Celios\Core\Console\Commands;

use Celios\Core\Services\Sitemap\SitemapGenerator;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate
                            {--clean : Force clear cache before generation}
                            {--path= : Custom output file path}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the XML sitemap file and refresh the cache';

    /**
     * Execute the console command.
     */
    public function handle(SitemapGenerator $generator): int
    {
        $startTime = microtime(true);
        $this->info('Starting sitemap generation...');

        if ($this->option('clean')) {
            $generator->clearCache();
            $this->comment('Sitemap cache cleared.');
        }

        $customPath = $this->option('path');
        $outputPath = $generator->writeToFile($customPath);

        $stats = $generator->getStats();
        $elapsed = round((microtime(true) - $startTime) * 1000, 2);

        $this->newLine();
        $this->info('✅ Sitemap generated successfully!');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Indexed URLs', $stats['total_urls']],
                ['CMS Pages', $stats['pages_count']],
                ['Blog Posts', $stats['posts_count']],
                ['Blog Categories', $stats['categories_count']],
                ['Static / Core Routes', $stats['static_count']],
                ['Custom URLs', $stats['custom_count']],
                ['Output File', $outputPath],
                ['File Size', $stats['static_size']],
                ['Execution Time', "{$elapsed} ms"],
            ]
        );

        return self::SUCCESS;
    }
}
