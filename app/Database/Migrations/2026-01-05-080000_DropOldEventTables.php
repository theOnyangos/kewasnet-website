<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropOldEventTables extends Migration
{
    public function up()
    {
        // Drop old event-related tables in reverse order of dependencies
        // The second parameter (true) means "if exists" - safe to drop even if table doesn't exist
        // Drop event_tickets first (has foreign keys)
        $this->forge->dropTable('event_tickets', true);

        // Drop event_registrations
        $this->forge->dropTable('event_registrations', true);

        // Drop event_organizers
        $this->forge->dropTable('event_organizers', true);

        // Drop old events table last
        $this->forge->dropTable('events', true);
    }

    public function down()
    {
        // This migration is one-way - we don't want to recreate old tables
        // The new migrations will create the proper tables
    }
}

