<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddResourceUrlsToCourseLectures extends Migration
{
    public function up()
    {
        // Make migration idempotent: only add each field when missing.
        if (! $this->db->fieldExists('resource_urls', 'course_lectures')) {
            $this->forge->addColumn('course_lectures', [
                'resource_urls' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'comment' => 'JSON array of resource URLs',
                    'after' => 'video_url',
                ],
            ]);
        }

        if (! $this->db->fieldExists('order_index', 'course_lectures')) {
            $this->forge->addColumn('course_lectures', [
                'order_index' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 1,
                    'comment' => 'Order of lecture within section',
                    'after' => 'duration',
                ],
            ]);
        }

        if (! $this->db->fieldExists('is_free_preview', 'course_lectures')) {
            $this->forge->addColumn('course_lectures', [
                'is_free_preview' => [
                    'type' => 'TINYINT',
                    'constraint' => 1,
                    'default' => 0,
                    'comment' => 'Allow non-enrolled users to view',
                    'after' => 'is_preview',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('resource_urls', 'course_lectures')) {
            $this->forge->dropColumn('course_lectures', 'resource_urls');
        }

        if ($this->db->fieldExists('order_index', 'course_lectures')) {
            $this->forge->dropColumn('course_lectures', 'order_index');
        }

        if ($this->db->fieldExists('is_free_preview', 'course_lectures')) {
            $this->forge->dropColumn('course_lectures', 'is_free_preview');
        }
    }
}
