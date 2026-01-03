<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseChatTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'chat_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'chat_course_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'chat_student_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'chat_message' => [
                'type' => 'TEXT',
            ],
            'chat_status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
            ],
            'chat_instructor_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'chat_title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'chat_created_at' => [
                'type' => 'DATETIME',
            ],
            'chat_updated_at' => [
                'type' => 'DATETIME',
            ],
            'chat_deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('chat_id', true);
        $this->forge->createTable('course_chats');
    }

    public function down()
    {
        $this->forge->dropTable('course_chats');
    }
}
