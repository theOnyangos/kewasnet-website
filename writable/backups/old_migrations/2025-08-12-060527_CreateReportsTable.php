<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

// Migration 10: Create Reports Table
class CreateReportsTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('reports')) {
            $this->forge->dropTable('reports', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'primary' => true,
                'default' => 'UUID()',
                'null' => false,
            ],
            'reporter_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'reportable_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false, // 'discussion' or 'reply'
            ],
            'reportable_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => false,
            ],
            'reason' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'reviewed', 'resolved', 'dismissed'],
                'default' => 'pending',
            ],
            'reviewed_by' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'action_taken' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->addKey(['reportable_type', 'reportable_id']);
        $this->forge->addKey(['status', 'created_at']);
        $this->forge->addKey('reporter_id');
        // Note: Foreign keys added after table creation to avoid constraint issues
        $this->forge->createTable('reports');
        
        // Add foreign keys after table is created
        try {
            if ($this->db->tableExists('users')) {
                $this->db->query("ALTER TABLE `reports` ADD CONSTRAINT `fk_reports_reporter` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE");
                $this->db->query("ALTER TABLE `reports` ADD CONSTRAINT `fk_reports_reviewed_by` FOREIGN KEY (`reviewed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE");
            }
        } catch (\Exception $e) {
            // Foreign key may already exist - ignore
        }
    }

    public function down()
    {
        $this->forge->dropTable('reports');
    }
}
