<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseReviewsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cr_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'cr_course_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'cr_student_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'cr_rating' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'cr_review' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'cr_status' => [
                'type' => 'ENUM',
                'constraint' => ['approved', 'rejected'],
                'default' => 'approved',
            ],
            'cr_created_at' => [
                'type' => 'DATETIME',
            ],
            'cr_updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'cr_deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addPrimaryKey('cr_id');
        $this->forge->createTable('course_reviews');
    }

    public function down()
    {
        $this->forge->dropTable('course_reviews', true);
    }
}
