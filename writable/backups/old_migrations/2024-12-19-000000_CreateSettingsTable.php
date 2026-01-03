<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Ramsey\Uuid\Uuid;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('settings')) {
            $this->forge->dropTable('settings', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
                'unique'     => true,
            ],
            'setting_key' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'setting_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'setting_type' => [
                'type'       => 'ENUM',
                'constraint' => ['string', 'integer', 'boolean', 'array', 'json'],
                'default'    => 'string',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('setting_key');
        $this->forge->createTable('settings');

        // Add some default sitemap settings
        $data = [
            [
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_auto_generate',
                'setting_value' => '0',
                'setting_type' => 'boolean',
                'description' => 'Automatically generate sitemap when content changes',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_include_images',
                'setting_value' => '1',
                'setting_type' => 'boolean',
                'description' => 'Include images in sitemap',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_max_urls',
                'setting_value' => '50000',
                'setting_type' => 'integer',
                'description' => 'Maximum number of URLs in sitemap',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_priority_default',
                'setting_value' => '0.5',
                'setting_type' => 'string',
                'description' => 'Default priority for sitemap URLs',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_changefreq_default',
                'setting_value' => 'monthly',
                'setting_type' => 'string',
                'description' => 'Default change frequency for sitemap URLs',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_exclude_patterns',
                'setting_value' => '',
                'setting_type' => 'string',
                'description' => 'URL patterns to exclude from sitemap (comma-separated)',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'setting_key' => 'sitemap_content_types',
                'setting_value' => '["static","blog","resources","events","jobs"]',
                'setting_type' => 'json',
                'description' => 'Content types to include in sitemap',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Only insert if settings don't already exist
        foreach ($data as $setting) {
            $exists = $this->db->table('settings')
                ->where('setting_key', $setting['setting_key'])
                ->countAllResults();
            
            if ($exists == 0) {
                $this->db->table('settings')->insert($setting);
            }
        }
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}
