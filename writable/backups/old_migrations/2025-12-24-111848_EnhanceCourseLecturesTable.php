<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceCourseLecturesTable extends Migration
{
    public function up()
    {
        // Check which columns already exist
        $existingColumns = [];
        if ($this->db->tableExists('course_lectures')) {
            $query = $this->db->query("SHOW COLUMNS FROM `course_lectures`");
            foreach ($query->getResultArray() as $row) {
                $existingColumns[] = $row['Field'];
            }
        }
        
        $fields = [
            'section_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'id',
            ],
            'vimeo_video_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'video_url',
            ],
            'order_index' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'is_preview',
            ],
            'is_free_preview' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'is_preview',
            ],
        ];

        // Only add columns that don't already exist
        $fieldsToAdd = [];
        foreach ($fields as $fieldName => $fieldDef) {
            if (!in_array($fieldName, $existingColumns)) {
                $fieldsToAdd[$fieldName] = $fieldDef;
            }
        }
        
        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('course_lectures', $fieldsToAdd);
        }
        
        // Add indexes (only if columns exist)
        try {
            if (in_array('section_id', $existingColumns) || !empty($fieldsToAdd['section_id'])) {
                $this->db->query('ALTER TABLE course_lectures ADD INDEX idx_section_id (section_id)');
            }
        } catch (\Exception $e) {
            // Index may already exist - ignore
        }
        
        try {
            if (in_array('order_index', $existingColumns) || !empty($fieldsToAdd['order_index'])) {
                $this->db->query('ALTER TABLE course_lectures ADD INDEX idx_order_index (order_index)');
            }
        } catch (\Exception $e) {
            // Index may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropColumn('course_lectures', ['section_id', 'vimeo_video_id', 'order_index', 'is_free_preview']);
    }
}
