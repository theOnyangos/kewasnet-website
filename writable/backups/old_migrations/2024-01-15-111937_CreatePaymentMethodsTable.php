<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentMethodsTable extends Migration
{
    public function up()
    {
        // Create the payment methods table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => TRUE,
                'auto_increment' => TRUE,
                'comment' => 'The payment method ID'
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The payment method name'
            ],
            'description' => [
                'type' => 'TEXT',
                'comment' => 'The payment method description'
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the payment method was created'
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the payment method was updated'
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the payment method was deleted'
            ]
        ]);

        $this->forge->addKey('id', TRUE);
        $this->forge->createTable('payment_methods');
    }

    public function down()
    {
        // Drop the payment methods table
        $this->forge->dropTable('payment_methods');
    }
}
