<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertRemainingTablesToUuid extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Convert empty tables that still have INT IDs
        $tables = [
            'lecture_attachments' => ['lecture_id'],
            'lecture_links' => ['lecture_id'],
            'vimeo_videos' => ['lecture_id'],
            'course_announcements' => ['course_id'],
            'course_carts' => ['course_id'],
            'course_goals' => ['course_id'],
            'course_requirements' => ['course_id'],
            'course_questions' => ['course_id', 'section_id'],
            'course_question_replies' => ['question_id'],
            'course_question_reply_likes' => ['question_id', 'reply_id'],
            'course_lecture_progress' => ['course_id', 'lecture_id'],
        ];
        
        foreach ($tables as $tableName => $foreignKeys) {
            if (!$db->tableExists($tableName)) {
                echo "⊘ $tableName does not exist, skipping\n";
                continue;
            }
            
            echo "Converting $tableName schema to UUID...\n";
            
            // Drop primary key and remove auto_increment from id
            try {
                $db->query("ALTER TABLE $tableName MODIFY COLUMN id INT(11)");
            } catch (\Exception $e) {
                echo "  Note: " . $e->getMessage() . "\n";
            }
            
            try {
                $db->query("ALTER TABLE $tableName DROP PRIMARY KEY");
            } catch (\Exception $e) {
                echo "  Note: " . $e->getMessage() . "\n";
            }
            
            // Change id column type to VARCHAR(36)
            $db->query("ALTER TABLE $tableName MODIFY COLUMN id VARCHAR(36) NOT NULL");
            
            // Add primary key back
            $db->query("ALTER TABLE $tableName ADD PRIMARY KEY (id)");
            
            // Change foreign key columns to VARCHAR(36)
            foreach ($foreignKeys as $fkColumn) {
                // Special handling for nullable columns
                $nullable = in_array($fkColumn, ['lecture_id', 'option_id', 'section_id']) ? 'NULL' : 'NOT NULL';
                
                try {
                    $db->query("ALTER TABLE $tableName MODIFY COLUMN $fkColumn VARCHAR(36) $nullable");
                    
                    // Add index on foreign key if it doesn't exist
                    $db->query("CREATE INDEX idx_$fkColumn ON $tableName ($fkColumn)");
                } catch (\Exception $e) {
                    echo "  Note: " . $e->getMessage() . "\n";
                }
            }
            
            echo "✓ $tableName schema converted to UUID\n";
        }
    }

    public function down()
    {
        // Not supported - would require schema changes back to INT
    }
}
