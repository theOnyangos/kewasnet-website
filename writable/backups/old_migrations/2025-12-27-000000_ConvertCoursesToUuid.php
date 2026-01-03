<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ConvertCoursesToUuid extends Migration
{
    public function up()
    {
        // Skip if courses table doesn't exist (it was dropped)
        if (!$this->db->tableExists('courses')) {
            return;
        }

        // Step 1: Create a temporary UUID column
        $fields = [
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
            ],
        ];
        $this->forge->addColumn('courses', $fields);
        
        // Step 2: Generate UUIDs for existing records
        $db = \Config\Database::connect();
        $query = $db->query("SELECT id FROM courses WHERE deleted_at IS NULL");
        $courses = $query->getResult();
        
        foreach ($courses as $course) {
            $uuid = $this->generateUUID();
            $db->table('courses')->where('id', $course->id)->update(['uuid' => $uuid]);
        }
        
        // Step 3: Create backup of related tables with course_id
        // Update course_sections
        $this->forge->addColumn('course_sections', [
            'course_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
            ]
        ]);
        
        $db->query("
            UPDATE course_sections cs
            JOIN courses c ON cs.course_id = c.id
            SET cs.course_uuid = c.uuid
        ");
        
        // Update course_lectures
        $this->forge->addColumn('course_lectures', [
            'course_uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
            ]
        ]);
        
        $db->query("
            UPDATE course_lectures cl
            JOIN courses c ON cl.course_id = c.id
            SET cl.course_uuid = c.uuid
        ");
        
        // Update course_enrollments if exists
        if ($db->tableExists('course_enrollments')) {
            $this->forge->addColumn('course_enrollments', [
                'course_uuid' => [
                    'type' => 'VARCHAR',
                    'constraint' => 36,
                    'null' => true,
                ]
            ]);
            
            $db->query("
                UPDATE course_enrollments ce
                JOIN courses c ON ce.course_id = c.id
                SET ce.course_uuid = c.uuid
            ");
        }
        
        // Update orders table if exists
        if ($db->tableExists('orders')) {
            $this->forge->addColumn('orders', [
                'course_uuid' => [
                    'type' => 'VARCHAR',
                    'constraint' => 36,
                    'null' => true,
                ]
            ]);
            
            $db->query("
                UPDATE orders o
                JOIN courses c ON o.course_id = c.id
                SET o.course_uuid = c.uuid
            ");
        }
        
        // Step 4: Drop old id column and rename uuid to id
        // First drop foreign key constraints if any
        $this->forge->dropKey('courses', 'PRIMARY');
        $this->forge->dropColumn('courses', 'id');
        
        // Rename uuid to id
        $db->query("ALTER TABLE courses CHANGE uuid id VARCHAR(36) NOT NULL");
        $db->query("ALTER TABLE courses ADD PRIMARY KEY (id)");
        $db->query("ALTER TABLE courses ADD INDEX idx_id (id)");
        
        // Step 5: Update related tables
        // course_sections
        $this->forge->dropColumn('course_sections', 'course_id');
        $db->query("ALTER TABLE course_sections CHANGE course_uuid course_id VARCHAR(36) NOT NULL");
        $db->query("ALTER TABLE course_sections ADD INDEX idx_course_id (course_id)");
        
        // course_lectures
        $this->forge->dropColumn('course_lectures', 'course_id');
        $db->query("ALTER TABLE course_lectures CHANGE course_uuid course_id VARCHAR(36) NOT NULL");
        $db->query("ALTER TABLE course_lectures ADD INDEX idx_course_id (course_id)");
        
        // course_enrollments
        if ($db->tableExists('course_enrollments')) {
            $this->forge->dropColumn('course_enrollments', 'course_id');
            $db->query("ALTER TABLE course_enrollments CHANGE course_uuid course_id VARCHAR(36) NOT NULL");
            $db->query("ALTER TABLE course_enrollments ADD INDEX idx_course_id (course_id)");
        }
        
        // orders
        if ($db->tableExists('orders')) {
            $this->forge->dropColumn('orders', 'course_id');
            $db->query("ALTER TABLE orders CHANGE course_uuid course_id VARCHAR(36) NOT NULL");
            $db->query("ALTER TABLE orders ADD INDEX idx_course_id (course_id)");
        }
    }

    public function down()
    {
        // This migration is not easily reversible due to data transformation
        // You would need to restore from backup if you need to roll back
        echo "This migration cannot be automatically reversed. Please restore from backup.";
    }
    
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
}
