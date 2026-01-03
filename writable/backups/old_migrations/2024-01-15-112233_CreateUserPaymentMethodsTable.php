<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserPaymentMethodsTable extends Migration
{
    public function up()
    {
        // Create user payment methods table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
                'comment' => 'The user payment method ID'
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'comment' => 'The user ID'
            ],
            'payment_method_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'comment' => 'The payment method ID'
            ],
            'account_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account number'
            ],
            'account_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account name'
            ],
            'account_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account reference'
            ],
            'account_phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account phone number'
            ],
            'cvv' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account CVV'
            ],
            'expiry_date' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account expiry date'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the user payment method was created'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the user payment method was updated'
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the user payment method was deleted'
            ]
        ]);

        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('user_payment_methods');
    }

    public function down()
    {
        // Drop the user_payment_methods table
        $this->forge->dropTable('user_payment_methods');
    }
}
