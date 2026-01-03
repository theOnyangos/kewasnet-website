<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOfflinePaymentsTable extends Migration
{
    public function up()
    {
        // Create offline payments table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
            ],
            'transaction_type' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'trans_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'trans_time' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'trans_amount' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'business_short_code' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'bill_ref_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'invoice_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_account_balance' => [
                'type' => 'DOUBLE',
            ],
            'third_party_trans_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'msisdn' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'middle_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('offline_payments');
    }

    public function down()
    {
        // Delete offline payments table
        $this->forge->dropTable('offline_payments');
    }
}
