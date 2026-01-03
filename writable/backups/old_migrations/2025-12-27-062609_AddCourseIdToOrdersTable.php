<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCourseIdToOrdersTable extends Migration
{
    public function up()
    {
        // Check if course_id column already exists
        if ($this->db->fieldExists('course_id', 'orders')) {
            return;
        }

        // Add course_id column to orders table
        $fields = [
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'after' => 'user_id',
            ],
        ];

        $this->forge->addColumn('orders', $fields);
    }

    public function down()
    {
        // Remove course_id column from orders table
        $this->forge->dropColumn('orders', 'course_id');
    }
}
