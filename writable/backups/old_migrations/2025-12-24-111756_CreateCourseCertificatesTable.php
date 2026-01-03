<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCourseCertificatesTable extends Migration
{
    public function up()
    {
        // Drop table if it exists to avoid conflicts
        if ($this->db->tableExists('course_certificates')) {
            $this->forge->dropTable('course_certificates', true);
        }
        
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'course_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'certificate_url' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
                'null' => true,
            ],
            'certificate_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'verification_code' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'issued_at' => [
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
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('course_id');
        // Note: certificate_number already has unique constraint in field definition, so no need for separate key
        // Note: verification_code already has unique constraint in field definition, so no need for separate key
        $this->forge->createTable('course_certificates');
    }

    public function down()
    {
        $this->forge->dropTable('course_certificates');
    }
}
