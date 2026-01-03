<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTaskIconsTable extends Migration
{
    public function up()
    {
        // Create the task_icons table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->createTable('task_icons');
    }

    public function down()
    {
        // Drop table task_icons
        $this->forge->dropTable('task_icons');
    }
}
