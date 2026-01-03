<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseSubscriptionTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'csub_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'csub_student_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'csub_course_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'csub_status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
            ],
            'csub_purchased_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'csub_expires_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'csub_cancelled_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'csub_type' => [
                'type' => 'ENUM',
                'constraint' => ['subscription', 'one_time_purchase'],
                'default' => 'subscription',
            ],
            'csub_created_at' => [
                'type' => 'DATETIME',
            ],
            'csub_updated_at' => [
                'type' => 'DATETIME',
            ],
            'csub_deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('csub_id', true);
        $this->forge->createTable('course_subscriptions');
    }

    public function down()
    {
        $this->forge->dropTable('course_subscriptions');
    }
}
