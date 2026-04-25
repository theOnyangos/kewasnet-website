<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrderIndexToCourseSections extends Migration
{
    public function up()
    {
        if (! $this->db->fieldExists('order_index', 'course_sections')) {
            $this->forge->addColumn('course_sections', [
                'order_index' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'default' => 1,
                    'comment' => 'Order of section within course',
                    'after' => 'description',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('order_index', 'course_sections')) {
            $this->forge->dropColumn('course_sections', 'order_index');
        }
    }
}
