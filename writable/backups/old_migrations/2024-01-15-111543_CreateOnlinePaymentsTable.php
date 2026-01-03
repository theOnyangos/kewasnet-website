<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOnlinePaymentsTable extends Migration
{
    public function up()
    {
        // Create online payments table
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
            'phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'transaction_date' => [
                'type' => 'DATETIME',
            ],
            'account_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'response_description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'merchant_request_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'checkout_request_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'customer_message' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'mpesa_receipt_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'result_description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'amount' => [
                'type' => 'INT',
                'constraint' => 11,
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
        $this->forge->createTable('online_payments');
    }

    public function down()
    {
        // Delete online payments table
        $this->forge->dropTable('online_payments');
    }
}
