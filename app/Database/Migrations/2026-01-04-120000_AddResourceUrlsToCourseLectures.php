<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResourceUrlsToCourseLectures extends Migration
{
    public function up()
    {
        $fields = [
            'resource_urls' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'JSON array of resource URLs',
                'after' => 'video_url'
            ],
            'order_index' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'comment' => 'Order of lecture within section',
                'after' => 'duration'
            ],
            'is_free_preview' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'comment' => 'Allow non-enrolled users to view',
                'after' => 'is_preview'
            ]
        ];

        $this->forge->addColumn('course_lectures', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('course_lectures', ['resource_urls', 'order_index', 'is_free_preview']);
    }
}
