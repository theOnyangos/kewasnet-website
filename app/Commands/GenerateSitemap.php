<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\SitemapService;

class GenerateSitemap extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'sitemap:generate';
    protected $description = 'Generate sitemap and store in database';

    public function run(array $params)
    {
        CLI::write('Starting sitemap generation...', 'yellow');
        
        $service = new SitemapService();
        $result = $service->generateSitemap();
        
        if ($result['success']) {
            CLI::write('SUCCESS: ' . $result['message'], 'green');
            if (isset($result['count'])) {
                CLI::write('URLs processed: ' . $result['count'], 'cyan');
            }
        } else {
            CLI::write('FAILED: ' . $result['message'], 'red');
        }
        
        // Show statistics
        CLI::write('', 'white');
        CLI::write('Sitemap Statistics:', 'yellow');
        $stats = $service->getStatistics();
        CLI::write('Total URLs: ' . $stats['total'], 'white');
        CLI::write('Active URLs: ' . $stats['active'], 'white');
        CLI::write('Inactive URLs: ' . $stats['inactive'], 'white');
        
        if (!empty($stats['by_category'])) {
            CLI::write('', 'white');
            CLI::write('By Category:', 'yellow');
            foreach ($stats['by_category'] as $category) {
                CLI::write('  ' . $category->category . ': ' . $category->count, 'white');
            }
        }
    }
}
