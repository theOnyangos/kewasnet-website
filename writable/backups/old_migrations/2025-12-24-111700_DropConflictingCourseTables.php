<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropConflictingCourseTables extends Migration
{
    public function up()
    {
        // Only drop course_sub_categories as it's not being recreated
        // The other tables will be created by new migrations in batch 75+
        // We don't drop them here to avoid conflicts
        
        $tablesToDrop = [
            'course_sub_categories',
        ];

        foreach ($tablesToDrop as $table) {
            if ($this->db->tableExists($table)) {
                $this->forge->dropTable($table, true);
            }
        }
        
        // Note: We're not dropping course_lectures, course_sections, or courses
        // as those will be enhanced, not replaced
    }

    public function down()
    {
        // This migration only drops tables, so down() doesn't recreate them
        // The original migrations will recreate them if needed
    }
}
