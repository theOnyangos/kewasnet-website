<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentOrderTables extends Migration
{
    public function up()
    {
        // Payment methods table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'comment' => 'The payment method ID',
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The payment method name',
            ],
            'description' => [
                'type' => 'TEXT',
                'comment' => 'The payment method description',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the payment method was created',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the payment method was updated',
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the payment method was deleted',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('payment_methods');

        // User payment methods table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
                'comment' => 'The user payment method ID',
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'The user ID',
            ],
            'payment_method_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'comment' => 'The payment method ID',
            ],
            'account_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account number',
            ],
            'account_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account name',
            ],
            'account_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account reference',
            ],
            'account_phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account phone number',
            ],
            'cvv' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account CVV',
            ],
            'expiry_date' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'The user account expiry date',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the user payment method was created',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the user payment method was updated',
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'comment' => 'The date and time the user payment method was deleted',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('user_payment_methods');

        // Orders table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'comment' => 'User UUID',
            ],
            'course_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'Course UUID',
            ],
            'country_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'order_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'amount' => [
                'type' => 'DOUBLE',
                'unsigned' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'completed', 'failed'],
                'default' => 'pending',
            ],
            'payment_method' => [
                'type' => 'ENUM',
                'constraint' => ['paypal', 'stripe', 'flutterwave', 'mpesa', 'paystack'],
            ],
            'payment_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('orders');

        // M-Pesa settings table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'consumer_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'consumer_secret' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'token_endpoint' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'business_short_code' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'pass_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'environment' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'sandbox',
            ],
            'account_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'party_b' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'transaction_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'transaction_desc' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'callback_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'register_url_endpoint' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'stk_push_endpoint' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'callback_endpoint' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'consumer_endpoint' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'is_url_registered' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'test_mode' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'null' => true,
            ],
            'enabled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
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
        $this->forge->createTable('mpesa_settings');

        // M-Pesa transactions table
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
            'first_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'middle_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'last_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'transaction_date' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'account_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'response_description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'merchant_request_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'checkout_request_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'customer_message' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'mpesa_receipt_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'result_description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'amount' => [
                'type' => 'DOUBLE',
                'constraint' => '8,2',
                'null' => true,
            ],
            'transaction_type' => [
                'type' => 'ENUM',
                'constraint' => ['online', 'offline'],
                'default' => 'online',
            ],
            'trans_id' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'business_short_code' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'bill_ref_number' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'org_account_balance' => [
                'type' => 'DOUBLE',
                'constraint' => '8,2',
            ],
            'MSISDN' => [
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
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('mpesa_transactions');

        // Paystack settings table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'public_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'secret_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'environment' => [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'default' => 'test',
            ],
            'currency' => [
                'type' => 'VARCHAR',
                'constraint' => 3,
                'default' => 'KES',
            ],
            'webhook_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'enabled' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'payment_url' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'temp_uuid' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('paystack_settings');

        // Paystack transactions table
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
            'amount' => [
                'type' => 'DOUBLE',
                'constraint' => '8,2',
            ],
            'status' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'reference' => [
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
        $this->forge->createTable('paystack_transactions');
    }

    public function down()
    {
        $this->forge->dropTable('paystack_transactions', true);
        $this->forge->dropTable('paystack_settings', true);
        $this->forge->dropTable('mpesa_transactions', true);
        $this->forge->dropTable('mpesa_settings', true);
        $this->forge->dropTable('orders', true);
        $this->forge->dropTable('user_payment_methods', true);
        $this->forge->dropTable('payment_methods', true);
    }
}
