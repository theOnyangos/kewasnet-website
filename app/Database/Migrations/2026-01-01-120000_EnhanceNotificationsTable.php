<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class EnhanceNotificationsTable extends Migration
{
    public function up()
    {
        // Add new fields to notifications table
        $fields = [
            'type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'default' => 'info',
                'after' => 'user_id',
                'comment' => 'Notification type: success, warning, info, error, system',
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'type',
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'message',
                'comment' => 'Lucide icon name',
            ],
            'action_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
                'after' => 'icon',
            ],
            'action_text' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'action_url',
            ],
            'reference_id' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'action_text',
                'comment' => 'ID of related entity',
            ],
            'reference_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'reference_id',
                'comment' => 'Type: blog, course, opportunity, forum, resource, etc.',
            ],
            'read_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status',
            ],
        ];

        $this->forge->addColumn('notifications', $fields);

        // Add indexes for better performance
        $this->db->query('ALTER TABLE notifications ADD INDEX idx_user_status (user_id, status)');
        $this->db->query('ALTER TABLE notifications ADD INDEX idx_user_created (user_id, created_at)');
        $this->db->query('ALTER TABLE notifications ADD INDEX idx_type (type)');
        $this->db->query('ALTER TABLE notifications ADD INDEX idx_reference (reference_type, reference_id)');
    }

    public function down()
    {
        // Drop indexes first
        $this->db->query('ALTER TABLE notifications DROP INDEX idx_user_status');
        $this->db->query('ALTER TABLE notifications DROP INDEX idx_user_created');
        $this->db->query('ALTER TABLE notifications DROP INDEX idx_type');
        $this->db->query('ALTER TABLE notifications DROP INDEX idx_reference');

        // Drop columns
        $this->forge->dropColumn('notifications', [
            'type',
            'title',
            'icon',
            'action_url',
            'action_text',
            'reference_id',
            'reference_type',
            'read_at',
        ]);
    }
}
