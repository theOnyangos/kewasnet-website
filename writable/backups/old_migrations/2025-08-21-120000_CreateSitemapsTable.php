<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSitemapsTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('sitemaps')) {
            $this->forge->dropTable('sitemaps', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type'       => 'CHAR',
                'constraint' => 36,
                'primary'    => true,
                'default'    => 'UUID()',
                'null'       => false,
            ],
            'url' => [
                'type'       => 'VARCHAR',
                'constraint' => '500',
                'null'       => false,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'description' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
                'default'    => 'Other Pages',
            ],
            'changefreq' => [
                'type'       => 'ENUM',
                'constraint' => ['always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never'],
                'default'    => 'monthly',
                'null'       => false,
            ],
            'priority' => [
                'type'       => 'DECIMAL',
                'constraint' => '2,1',
                'default'    => '0.5',
                'null'       => false,
            ],
            'last_modified' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'is_active' => [
                'type'       => 'BOOLEAN',
                'default'    => true,
                'null'       => false,
            ],
            'created_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
            'updated_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
            'deleted_at' => [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
            ],
        ]);
        
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('url');
        $this->forge->addKey('category');
        $this->forge->addKey('is_active');
        $this->forge->addKey('last_modified');
        $this->forge->createTable('sitemaps');
    }

    public function down()
    {
        $this->forge->dropTable('sitemaps');
    }
}
