<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CreateSitemapTable extends BaseCommand
{
    protected $group = 'Database';
    protected $name = 'create:sitemap-table';
    protected $description = 'Create the sitemap table manually';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        $sql = "CREATE TABLE IF NOT EXISTS `sitemaps` (
            `id` CHAR(36) NOT NULL,
            `url` VARCHAR(500) NOT NULL,
            `title` VARCHAR(255) NULL,
            `description` TEXT NULL,
            `category` VARCHAR(100) NOT NULL DEFAULT 'Other Pages',
            `changefreq` ENUM('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never') NOT NULL DEFAULT 'monthly',
            `priority` DECIMAL(2,1) NOT NULL DEFAULT '0.5',
            `last_modified` DATETIME NOT NULL,
            `is_active` BOOLEAN NOT NULL DEFAULT TRUE,
            `created_at` DATETIME NULL,
            `updated_at` DATETIME NULL,
            `deleted_at` DATETIME NULL,
            PRIMARY KEY (`id`),
            KEY `idx_sitemaps_url` (`url`),
            KEY `idx_sitemaps_category` (`category`),
            KEY `idx_sitemaps_is_active` (`is_active`),
            KEY `idx_sitemaps_last_modified` (`last_modified`)
        ) DEFAULT CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci;";

        try {
            $db->query($sql);
            CLI::write('Sitemap table created successfully!', 'green');
        } catch (\Exception $e) {
            CLI::write('Error creating sitemap table: ' . $e->getMessage(), 'red');
        }
    }
}
