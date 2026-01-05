<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEventBookingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'event_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'Null for guest bookings',
            ],
            'booking_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'total_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'payment_status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'paid', 'failed', 'refunded'],
                'default' => 'pending',
            ],
            'payment_reference' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'order_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
                'comment' => 'Link to orders table if paid',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['confirmed', 'cancelled'],
                'default' => 'confirmed',
            ],
            'email' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'phone' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
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
        $this->forge->addKey('event_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('payment_status');
        $this->forge->addUniqueKey('booking_number');
        $this->forge->createTable('event_bookings');
    }

    public function down()
    {
        $this->forge->dropTable('event_bookings', true);
    }
}

