<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use Ramsey\Uuid\Uuid;

class ConvertEmailQueueIdToUUID extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Check current table structure
        $columns = $db->getFieldData('email_queue');
        $columnNames = array_column($columns, 'name');
        $hasIdUuid = in_array('id_uuid', $columnNames);
        $hasIdInt = in_array('id', $columnNames);
        $idIsInt = false;
        
        if ($hasIdInt) {
            foreach ($columns as $col) {
                if ($col->name === 'id') {
                    $idIsInt = (stripos($col->type, 'int') !== false);
                    break;
                }
            }
        }
        
        // If id_uuid already exists, we're in the middle of migration - continue from there
        if ($hasIdUuid && $hasIdInt && $idIsInt) {
            // Step 4: Generate UUIDs for existing records that don't have one
            $emails = $db->table('email_queue')
                        ->where('id_uuid IS NULL')
                        ->get()
                        ->getResultArray();
            
            foreach ($emails as $email) {
                $uuid = Uuid::uuid4()->toString();
                $db->table('email_queue')
                   ->where('id', $email['id'])
                   ->update(['id_uuid' => $uuid]);
            }

            // Step 5: Drop the old primary key constraint if it exists
            try {
                $this->db->query('ALTER TABLE email_queue DROP PRIMARY KEY');
            } catch (\Exception $e) {
                // Primary key might already be dropped, continue
            }

            // Step 6: Drop the old id column
            $this->forge->dropColumn('email_queue', 'id');

            // Step 7: Rename id_uuid to id using raw SQL
            $this->db->query('ALTER TABLE email_queue CHANGE id_uuid id VARCHAR(36) NOT NULL');

            // Step 8: Add the new primary key
            $this->db->query('ALTER TABLE email_queue ADD PRIMARY KEY (id)');
            return;
        }
        
        // If id is already VARCHAR(36), migration is complete
        if ($hasIdInt && !$idIsInt) {
            // Already converted, skip
            return;
        }
        
        // Normal migration path
        // Step 1: Remove auto_increment from existing id column (but keep it as INT for now)
        $this->db->query('ALTER TABLE email_queue MODIFY id INT(11) UNSIGNED NOT NULL');
        
        // Step 2: Drop the primary key constraint temporarily
        $this->db->query('ALTER TABLE email_queue DROP PRIMARY KEY');
        
        // Step 3: Add a temporary UUID column (only if it doesn't exist)
        if (!$hasIdUuid) {
            $this->forge->addColumn('email_queue', [
                'id_uuid' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 36,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        // Step 4: Generate UUIDs for existing records
        $emails = $db->table('email_queue')->get()->getResultArray();
        
        foreach ($emails as $email) {
            if (empty($email['id_uuid'] ?? null)) {
                $uuid = Uuid::uuid4()->toString();
                $db->table('email_queue')
                   ->where('id', $email['id'])
                   ->update(['id_uuid' => $uuid]);
            }
        }

        // Step 5: Drop the old id column
        $this->forge->dropColumn('email_queue', 'id');

        // Step 6: Rename id_uuid to id using raw SQL
        $this->db->query('ALTER TABLE email_queue CHANGE id_uuid id VARCHAR(36) NOT NULL');

        // Step 7: Add the new primary key
        $this->db->query('ALTER TABLE email_queue ADD PRIMARY KEY (id)');
    }

    public function down()
    {
        $db = \Config\Database::connect();
        
        // Step 1: Add a temporary INT column
        $this->forge->addColumn('email_queue', [
            'id_int' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => false,
                'null'           => true,
                'after'          => 'id',
            ],
        ]);

        // Step 2: Generate sequential IDs for existing records
        $emails = $db->table('email_queue')->orderBy('created_at', 'ASC')->get()->getResultArray();
        
        $counter = 1;
        foreach ($emails as $email) {
            $db->table('email_queue')
               ->where('id', $email['id'])
               ->update(['id_int' => $counter++]);
        }

        // Step 3: Drop the old primary key constraint
        $this->db->query('ALTER TABLE email_queue DROP PRIMARY KEY');

        // Step 4: Drop the old UUID id column
        $this->forge->dropColumn('email_queue', 'id');

        // Step 5: Rename id_int to id and make it auto_increment using raw SQL
        $this->db->query('ALTER TABLE email_queue CHANGE id_int id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT');

        // Step 6: Add the new primary key
        $this->db->query('ALTER TABLE email_queue ADD PRIMARY KEY (id)');
    }
}
