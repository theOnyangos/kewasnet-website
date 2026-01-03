<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoursePurchaseTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cp_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'cp_student_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'cp_course_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'cp_status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
            ],
            'cp_purchased_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'cp_created_at' => [
                'type' => 'DATETIME',
            ],
            'cp_updated_at' => [
                'type' => 'DATETIME',
            ],
            'cp_deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('cp_id', true);
        $this->forge->createTable('course_purchases');
    }

    public function down()
    {
        $this->forge->dropTable('course_purchases');
    }
}
