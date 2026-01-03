<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertAllLearningHubTablesToUuid extends Migration
{
    private function generateUUID(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    public function up()
    {
        $db = \Config\Database::connect();
        
        // Convert tables in order of dependencies
        $this->convertTableIdToUuid('course_sections', ['course_id' => 'courses']);
        $this->convertTableIdToUuid('quizzes', ['course_section_id' => 'course_sections']);
        $this->convertTableIdToUuid('quiz_questions', ['quiz_id' => 'quizzes']);
        $this->convertTableIdToUuid('quiz_question_options', ['question_id' => 'quiz_questions']);
        $this->convertTableIdToUuid('quiz_attempts', ['quiz_id' => 'quizzes']);
        $this->convertTableIdToUuid('quiz_answers', ['attempt_id' => 'quiz_attempts', 'question_id' => 'quiz_questions', 'option_id' => 'quiz_question_options']);
        $this->convertTableIdToUuid('lecture_attachments', ['lecture_id' => 'course_lectures']);
        $this->convertTableIdToUuid('lecture_links', ['lecture_id' => 'course_lectures']);
        $this->convertTableIdToUuid('course_instructors', ['course_id' => 'courses']);
        $this->convertTableIdToUuid('course_certificates', ['course_id' => 'courses']);
        $this->convertTableIdToUuid('course_goals', ['course_id' => 'courses']);
        $this->convertTableIdToUuid('course_requirements', ['course_id' => 'courses']);
        $this->convertTableIdToUuid('course_announcements', ['course_id' => 'courses']);
        $this->convertTableIdToUuid('course_carts', ['course_id' => 'courses']);
        $this->convertTableIdToUuid('course_questions', ['course_id' => 'courses']);
        $this->convertTableIdToUuid('course_question_replies', ['question_id' => 'course_questions']);
        $this->convertTableIdToUuid('course_question_reply_likes', ['reply_id' => 'course_question_replies']);
        $this->convertTableIdToUuid('course_lecture_progress', ['course_id' => 'courses', 'lecture_id' => 'course_lectures']);
        $this->convertTableIdToUuid('vimeo_videos', ['lecture_id' => 'course_lectures']);
        $this->convertTableIdToUuid('user_progress', ['course_id' => 'courses', 'lecture_id' => 'course_lectures']);
    }

    private function convertTableIdToUuid(string $tableName, array $foreignKeyRefs = [])
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists($tableName)) {
            echo "⊘ $tableName does not exist, skipping\n";
            return;
        }
        
        echo "Converting $tableName to UUID...\n";
        
        // Step 1: Create a mapping table in memory
        $idMapping = [];
        $records = $db->table($tableName)->select('id')->get()->getResult();
        foreach ($records as $record) {
            $idMapping[$record->id] = $this->generateUUID();
        }
        
        if (empty($idMapping)) {
            echo "Table $tableName has no records, converting schema only\n";
            
            // For empty tables, just convert the column types
            // Drop primary key and remove auto_increment from id
            try {
                $db->query("ALTER TABLE $tableName MODIFY COLUMN id INT(11)");
            } catch (\Exception $e) {}
            
            try {
                $db->query("ALTER TABLE $tableName DROP PRIMARY KEY");
            } catch (\Exception $e) {}
            
            // Change id column type to VARCHAR(36)
            $db->query("ALTER TABLE $tableName MODIFY COLUMN id VARCHAR(36) NOT NULL");
            
            // Add primary key back
            $db->query("ALTER TABLE $tableName ADD PRIMARY KEY (id)");
            
            // Change foreign key columns to VARCHAR(36)
            foreach ($foreignKeyRefs as $fkColumn => $refTable) {
                $nullable = ($fkColumn === 'option_id' || $fkColumn === 'lecture_id') ? 'NULL' : 'NOT NULL';
                $db->query("ALTER TABLE $tableName MODIFY COLUMN $fkColumn VARCHAR(36) $nullable");
                
                // Add index on foreign key
                try {
                    $db->query("CREATE INDEX idx_$fkColumn ON $tableName ($fkColumn)");
                } catch (\Exception $e) {}
            }
            
            echo "✓ $tableName schema converted to UUID\n";
            return;
        }
        
        // Step 2: Add temp_id column for UUID
        $columns = $db->getFieldNames($tableName);
        if (!in_array('temp_id', $columns)) {
            $this->forge->addColumn($tableName, [
                'temp_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 36,
                    'null' => true,
                ]
            ]);
        }
        
        // Step 3: Populate temp_id with UUIDs
        foreach ($idMapping as $oldId => $uuid) {
            $db->table($tableName)->where('id', $oldId)->update(['temp_id' => $uuid]);
        }
        
        // Step 4: For each foreign key, add temp column and update it
        foreach ($foreignKeyRefs as $fkColumn => $refTable) {
            $tempFkCol = "temp_$fkColumn";
            
            // Add temp foreign key column
            $columns = $db->getFieldNames($tableName);
            if (!in_array($tempFkCol, $columns)) {
                $this->forge->addColumn($tableName, [
                    $tempFkCol => [
                        'type' => 'VARCHAR',
                        'constraint' => 36,
                        'null' => true,
                    ]
                ]);
            }
            
            // Get the referenced table's ID mapping
            if ($db->tableExists($refTable)) {
                // Check if referenced table has temp_id column
                $columns = $db->getFieldNames($refTable);
                if (in_array('temp_id', $columns)) {
                    // Referenced table is currently being converted - use temp_id
                    $refRecords = $db->table($refTable)->select('id, temp_id')->get()->getResult();
                    foreach ($refRecords as $refRecord) {
                        if (!empty($refRecord->temp_id)) {
                            $db->table($tableName)
                                ->where($fkColumn, $refRecord->id)
                                ->update([$tempFkCol => $refRecord->temp_id]);
                        }
                    }
                } else {
                    // Referenced table already converted or has UUID - check id column type
                    $firstRef = $db->table($refTable)->limit(1)->get()->getRow();
                    if ($firstRef && strlen($firstRef->id) == 36) {
                        // Referenced table already has UUID
                        $db->query("UPDATE $tableName SET $tempFkCol = $fkColumn WHERE $fkColumn IS NOT NULL");
                    }
                }
            }
        }
        
        // Step 5: Drop primary key and remove auto_increment from id
        try {
            $db->query("ALTER TABLE $tableName MODIFY COLUMN id INT(11)");
        } catch (\Exception $e) {}
        
        try {
            $db->query("ALTER TABLE $tableName DROP PRIMARY KEY");
        } catch (\Exception $e) {}
        
        // Step 6: Change id column type to VARCHAR(36)
        $db->query("ALTER TABLE $tableName MODIFY COLUMN id VARCHAR(36) NOT NULL");
        
        // Step 7: Update id values from temp_id
        $db->query("UPDATE $tableName SET id = temp_id WHERE temp_id IS NOT NULL");
        
        // Step 8: Change foreign key columns to VARCHAR(36)
        foreach ($foreignKeyRefs as $fkColumn => $refTable) {
            $tempFkCol = "temp_$fkColumn";
            $nullable = ($fkColumn === 'option_id' || $fkColumn === 'lecture_id') ? 'NULL' : 'NOT NULL';
            
            $db->query("ALTER TABLE $tableName MODIFY COLUMN $fkColumn VARCHAR(36) $nullable");
            $db->query("UPDATE $tableName SET $fkColumn = $tempFkCol WHERE $tempFkCol IS NOT NULL");
            
            // Drop temp column
            $this->forge->dropColumn($tableName, $tempFkCol);
        }
        
        // Step 9: Add primary key back
        $db->query("ALTER TABLE $tableName ADD PRIMARY KEY (id)");
        
        // Step 10: Drop temp_id column
        $this->forge->dropColumn($tableName, 'temp_id');
        
        // Step 11: Add indexes on foreign keys
        foreach ($foreignKeyRefs as $fkColumn => $refTable) {
            try {
                $db->query("CREATE INDEX idx_$fkColumn ON $tableName ($fkColumn)");
            } catch (\Exception $e) {}
        }
        
        echo "✓ $tableName converted to UUID\n";
    }

    public function down()
    {
        // Not supported - would require recreating tables
    }
}
