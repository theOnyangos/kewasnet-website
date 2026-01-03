<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class SitemapSettingsSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => '6b4115c8-0c9c-499d-91d5-8aaa42f84421',
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_auto_generate',
                'setting_value' => '0',
                'setting_type' => 'boolean',
                'description' => 'Automatically generate sitemap when content changes',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '50c2e678-2155-4b39-85b6-0a3755fdb476',
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_include_images',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'description' => 'Include images in sitemap',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 'ff916ed4-c690-48b8-9f0c-2b9cc492c35f',
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_max_urls',
                'setting_value' => '50000',
                'setting_type' => 'integer',
                'description' => 'Maximum number of URLs in sitemap',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '1470292e-17dc-4931-a1b1-3800a7f986cd',
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_priority_default',
                'setting_value' => '0.5',
                'setting_type' => 'string',
                'description' => 'Default priority for sitemap URLs',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 'f6182977-9b47-4a0c-ad0a-84e7303367d1',
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_changefreq_default',
                'setting_value' => 'monthly',
                'setting_type' => 'string',
                'description' => 'Default change frequency for sitemap URLs',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 'fee53eda-4a00-4a9c-9810-ae672d64c0a6',
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_exclude_patterns',
                'setting_value' => '',
                'setting_type' => 'string',
                'description' => 'URL patterns to exclude from sitemap (comma-separated)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 'c650e123-e87f-4855-9465-a5f43b6004ed',
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_content_types',
                'setting_value' => json_encode(['static', 'blog', 'resources', 'events', 'jobs']),
                'setting_type' => 'json',
                'description' => 'Content types to include in sitemap',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => 'a3c93de4-80eb-4b98-857e-50e330284f9c',
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_last_generated',
                'setting_value' => '',
                'setting_type' => 'string',
                'description' => 'Timestamp of last sitemap generation',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => '6201c10c-642e-49b5-bb51-e363e9059edc',
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_base_url',
                'setting_value' => base_url(),
                'setting_type' => 'string',
                'description' => 'Base URL for sitemap generation',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Using insertBatch for better performance
        $this->db->table('sitemap_settings')->insertBatch($data);

        echo 'Sitemap settings seeded successfully.' . PHP_EOL;
    }
}