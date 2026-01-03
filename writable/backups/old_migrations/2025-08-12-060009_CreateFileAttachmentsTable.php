<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Migration 7: Create File Attachments Table
class CreateFileAttachmentsTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('file_attachments')) {
            $this->forge->dropTable('file_attachments', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()',
                'null' => false,
            ],
            'attachable_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false, // 'discussion' or 'reply'
            ],
            'attachable_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'original_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => false,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => false,
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'file_size' => [
                'type' => 'BIGINT',
                'null' => false,
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'download_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'is_image' => [
                'type' => 'BOOLEAN',
                'default' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['attachable_type', 'attachable_id']);
        $this->forge->addKey('user_id');
        $this->forge->addKey('file_type');
        // Note: Foreign key added after table creation to avoid constraint issues
        $this->forge->createTable('file_attachments');
        
        // Add foreign key after table is created
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `file_attachments` ADD CONSTRAINT `fk_file_attachments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('file_attachments');
    }
}
