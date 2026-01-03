<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
            ],
            'category_id' => [
                'type' => 'INT',
                'constraint' => 5,
            ],
            'sub_category_id' => [
                'type' => 'INT',
                'constraint' => 5,
                'null' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'summary' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'certificate' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0

            ],
            'level' => [
                'type' => 'ENUM',
                'constraint' => ['beginner', 'intermediate', 'advanced'],
                'default' => 'beginner'
            ],
            'price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'discount_price' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
            ],
            'duration' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'resources' => [
                'type' => 'TEXT',
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'image_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 1,
                'default' => 0
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'star_rating' => [
                'type' => 'DECIMAL',
                'constraint' => '3,2',
            ],
            'goals' => [
                'type' => 'TEXT',
            ],
            'instructor_id' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'preview_video_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'language' => [
                'type' => 'ENUM',
                'constraint' => ['english', 'french', 'spanish', 'swahili', 'german', 'italian', 'portuguese', 'russian', 'chinese', 'arabic'],
                'default' => 'english'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true
            ],

        ]);

        $this->forge->addKey('id');
        $this->forge->createTable('courses');
    }

    public function down()
    {
        $this->forge->dropTable('courses');
    }
}
