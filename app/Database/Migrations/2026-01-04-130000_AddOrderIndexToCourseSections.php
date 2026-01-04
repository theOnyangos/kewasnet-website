<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOrderIndexToCourseSections extends Migration
{
    public function up()
    {
        $fields = [
            'order_index' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 1,
                'comment' => 'Order of section within course',
                'after' => 'description'
            ]
        ];

        $this->forge->addColumn('course_sections', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('course_sections', 'order_index');
    }
}
