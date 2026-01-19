<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAIKnowledgeBaseTables extends Migration
{
    public function up()
    {
        // AI Knowledge Base Sources
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'comment' => 'UUID primary key',
            ],
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['text', 'url', 'file'],
                'comment' => 'Source type',
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'comment' => 'Human friendly title',
            ],
            'source_url' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'URL for url sources',
            ],
            'file_path' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Stored file path for file sources',
            ],
            'content_raw' => [
                'type' => 'LONGTEXT',
                'null' => true,
                'comment' => 'Raw/extracted text used for chunking',
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'disabled'],
                'default' => 'active',
                'comment' => 'Whether source is active for retrieval',
            ],
            'created_by' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
                'comment' => 'Admin user id that created the source',
            ],
            'last_ingested_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'comment' => 'Last ingestion timestamp',
            ],
            'ingest_error' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Last ingestion error (if any)',
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
        $this->forge->addKey('type');
        $this->forge->addKey('status');
        $this->forge->addKey('created_by');
        $this->forge->addKey('last_ingested_at');
        $this->forge->createTable('ai_kb_sources');

        // AI Knowledge Base Chunks
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'comment' => 'UUID primary key',
            ],
            'source_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'comment' => 'Foreign key to ai_kb_sources.id',
            ],
            'chunk_index' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'default' => 0,
                'comment' => 'Chunk position within the source',
            ],
            'content' => [
                'type' => 'LONGTEXT',
                'comment' => 'Chunk content used for retrieval',
            ],
            'metadata' => [
                'type' => 'JSON',
                'null' => true,
                'comment' => 'Chunk metadata (title, url, headings, etc.)',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('source_id');
        $this->forge->addKey('chunk_index');
        $this->forge->addKey('created_at');
        $this->forge->addUniqueKey(['source_id', 'chunk_index'], 'ai_kb_source_chunk_unique');
        $this->forge->addForeignKey('source_id', 'ai_kb_sources', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('ai_kb_chunks');
    }

    public function down()
    {
        $this->forge->dropTable('ai_kb_chunks', true);
        $this->forge->dropTable('ai_kb_sources', true);
    }
}

