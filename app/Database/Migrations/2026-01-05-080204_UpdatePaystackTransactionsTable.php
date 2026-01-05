<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdatePaystackTransactionsTable extends Migration
{
    public function up()
    {
        // Check if table exists
        if ($this->db->tableExists('paystack_transactions')) {
            // Add event_id if it doesn't exist
            if (!$this->db->fieldExists('event_id', 'paystack_transactions')) {
                $this->forge->addColumn('paystack_transactions', [
                    'event_id' => [
                        'type' => 'VARCHAR',
                        'constraint' => 36,
                        'null' => true,
                        'after' => 'user_id',
                    ],
                ]);
            }
            
            // Add course_id if it doesn't exist
            if (!$this->db->fieldExists('course_id', 'paystack_transactions')) {
                $this->forge->addColumn('paystack_transactions', [
                    'course_id' => [
                        'type' => 'VARCHAR',
                        'constraint' => 36,
                        'null' => true,
                        'after' => 'event_id',
                    ],
                ]);
            }
            
            // Add indexes for better query performance (only if columns exist)
            if ($this->db->fieldExists('event_id', 'paystack_transactions')) {
                try {
                    $this->db->query('ALTER TABLE `paystack_transactions` ADD INDEX `idx_event_id` (`event_id`)');
                } catch (\Exception $e) {
                    // Index might already exist, ignore
                }
            }
            
            if ($this->db->fieldExists('course_id', 'paystack_transactions')) {
                try {
                    $this->db->query('ALTER TABLE `paystack_transactions` ADD INDEX `idx_course_id` (`course_id`)');
                } catch (\Exception $e) {
                    // Index might already exist, ignore
                }
            }
        } else {
            // Create table if it doesn't exist
            $this->forge->addField([
                'id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'auto_increment' => true,
                ],
                'user_id' => [
                    'type' => 'INT',
                    'constraint' => 11,
                    'null' => true,
                ],
                'event_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 36,
                    'null' => true,
                ],
                'course_id' => [
                    'type' => 'VARCHAR',
                    'constraint' => 36,
                    'null' => true,
                ],
                'amount' => [
                    'type' => 'DOUBLE',
                    'constraint' => '10,2',
                ],
                'status' => [
                    'type' => 'VARCHAR',
                    'constraint' => 50,
                    'default' => 'pending',
                ],
                'reference' => [
                    'type' => 'VARCHAR',
                    'constraint' => 255,
                    'null' => true,
                ],
                'deleted_at' => [
                    'type' => 'DATETIME',
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
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey('user_id');
            $this->forge->addKey('event_id');
            $this->forge->addKey('course_id');
            $this->forge->addKey('reference');
            $this->forge->createTable('paystack_transactions');
        }
    }

    public function down()
    {
        // Don't drop the table, just remove the new columns if needed
        if ($this->db->tableExists('paystack_transactions')) {
            if ($this->db->fieldExists('event_id', 'paystack_transactions')) {
                $this->forge->dropColumn('paystack_transactions', 'event_id');
            }
            if ($this->db->fieldExists('course_id', 'paystack_transactions')) {
                $this->forge->dropColumn('paystack_transactions', 'course_id');
            }
        }
    }
}

