<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrgHomeTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'org_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'org_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'org_image_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'org_video_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'org_doc_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'org_published_state' => [
                'type' => 'ENUM',
                'constraint' => ['draft', 'published'],
                'default' => 'draft',
            ],
            'org_created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'org_updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'org_deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('org_id', true);
        $this->forge->createTable('org_home');
    }

    public function down()
    {
        $this->forge->dropTable('org_home');
    }
}
