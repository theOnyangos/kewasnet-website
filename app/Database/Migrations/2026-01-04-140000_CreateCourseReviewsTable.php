<?php
namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseReviewsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'course_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'rating' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => false,
            ],
            'review' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('course_id');
        $this->forge->addKey('user_id');
        $this->forge->createTable('course_reviews');
    }

    public function down()
    {
        $this->forge->dropTable('course_reviews', true);
    }
}
