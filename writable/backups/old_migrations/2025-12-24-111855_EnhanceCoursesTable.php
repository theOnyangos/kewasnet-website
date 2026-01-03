<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceCoursesTable extends Migration
{
    public function up()
    {
        $fields = [
            'is_paid' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'price',
            ],
            'vimeo_embed_settings' => [
                'type' => 'JSON',
                'null' => true,
                'after' => 'preview_video_url',
            ],
        ];

        $this->forge->addColumn('courses', $fields);
        
        // Add index
        $this->db->query('ALTER TABLE courses ADD INDEX idx_is_paid (is_paid)');
    }

    public function down()
    {
        $this->forge->dropColumn('courses', ['is_paid', 'vimeo_embed_settings']);
    }
}
