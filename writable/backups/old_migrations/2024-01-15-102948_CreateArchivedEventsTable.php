<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateArchivedEventsTable extends Migration
{
    public function up()
    {
        // Create the archived events table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'event_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'archived_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('archived_events');
    }

    public function down()
    {
        // Drop the archived events table
        $this->forge->dropTable('archived_events');
    }
}
