<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceCourseSectionsTable extends Migration
{
    public function up()
    {
        $fields = [
            'order_index' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'description',
            ],
            'quiz_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'order_index',
            ],
        ];

        $this->forge->addColumn('course_sections', $fields);
        
        // Add indexes
        $this->db->query('ALTER TABLE course_sections ADD INDEX idx_order_index (order_index)');
        $this->db->query('ALTER TABLE course_sections ADD INDEX idx_quiz_id (quiz_id)');
    }

    public function down()
    {
        $this->forge->dropColumn('course_sections', ['order_index', 'quiz_id']);
    }
}
