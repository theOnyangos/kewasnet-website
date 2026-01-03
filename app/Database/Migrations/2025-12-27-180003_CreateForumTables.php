<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateForumTables extends Migration
{
    public function up()
    {
        // Forums table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'icon' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'default' => '#3B82F6',
            ],
            'is_draft' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'sort_order' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'total_discussions' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'total_replies' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'last_activity_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_by' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey(['is_active', 'sort_order']);
        $this->forge->createTable('forums');

        // Forum members table
        $this->forge->addField([
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'forum_id' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
            ],
            'joined_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('forum_members');

        // Forum moderators table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'forum_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'moderator_title' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'assigned_by' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'assigned_at' => [
                'type' => 'DATETIME',
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['forum_id', 'user_id']);
        $this->forge->addKey(['forum_id', 'is_active']);
        $this->forge->addForeignKey('forum_id', 'forums', 'id', 'CASCADE', 'CASCADE', 'fk_forum_moderators_forum');
        $this->forge->createTable('forum_moderators');

        // Discussion tags table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'color' => [
                'type' => 'VARCHAR',
                'constraint' => 7,
                'default' => '#6B7280',
            ],
            'usage_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('usage_count');
        $this->forge->createTable('discussion_tags');

        // Discussions table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'forum_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'slug' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'content' => [
                'type' => 'LONGTEXT',
            ],
            'tags' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'is_pinned' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'is_locked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'is_featured' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'view_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'reply_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'like_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'last_reply_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'last_reply_by' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'hidden', 'reported', 'deleted'],
                'default' => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('slug');
        $this->forge->addKey(['forum_id', 'status', 'is_pinned', 'last_reply_at']);
        $this->forge->addKey(['user_id', 'created_at']);
        $this->forge->addKey('last_reply_at');
        $this->forge->addForeignKey('forum_id', 'forums', 'id', 'CASCADE', 'CASCADE', 'fk_discussions_forum');
        $this->forge->createTable('discussions');

        // Discussion tag pivot table
        $this->forge->addField([
            'discussion_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'tag_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey(['discussion_id', 'tag_id'], true);
        $this->forge->addKey('tag_id');
        $this->forge->addForeignKey('discussion_id', 'discussions', 'id', 'CASCADE', 'CASCADE', 'discussion_tag_pivot_discussion_id_foreign');
        $this->forge->addForeignKey('tag_id', 'discussion_tags', 'id', 'CASCADE', 'CASCADE', 'discussion_tag_pivot_tag_id_foreign');
        $this->forge->createTable('discussion_tag_pivot');

        // Discussion views table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'discussion_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'ip_address' => [
                'type' => 'VARCHAR',
                'constraint' => 45,
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'viewed_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['discussion_id', 'viewed_at']);
        $this->forge->addKey('user_id');
        $this->forge->addKey('ip_address');
        $this->forge->addForeignKey('discussion_id', 'discussions', 'id', 'CASCADE', 'CASCADE', 'fk_discussion_views_discussion');
        $this->forge->createTable('discussion_views');

        // Replies table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'discussion_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'parent_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'content' => [
                'type' => 'LONGTEXT',
            ],
            'like_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'is_best_answer' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'hidden', 'reported', 'deleted'],
                'default' => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['discussion_id', 'status', 'created_at']);
        $this->forge->addKey(['user_id', 'created_at']);
        $this->forge->addKey('parent_id');
        $this->forge->addKey('is_best_answer');
        $this->forge->addForeignKey('discussion_id', 'discussions', 'id', 'CASCADE', 'CASCADE', 'fk_replies_discussion');
        $this->forge->addForeignKey('parent_id', 'replies', 'id', 'CASCADE', 'CASCADE', 'fk_replies_parent');
        $this->forge->createTable('replies');

        // Likes table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'likeable_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'likeable_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'likeable_type', 'likeable_id']);
        $this->forge->addKey(['likeable_type', 'likeable_id']);
        $this->forge->createTable('likes');

        // Bookmarks table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'discussion_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'discussion_id']);
        $this->forge->addKey('discussion_id');
        $this->forge->addForeignKey('discussion_id', 'discussions', 'id', 'CASCADE', 'CASCADE', 'fk_bookmarks_discussion');
        $this->forge->createTable('bookmarks');

        // Reports table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'reporter_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'reportable_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'reportable_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'reason' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['pending', 'reviewed', 'resolved', 'dismissed'],
                'default' => 'pending',
            ],
            'reviewed_by' => [
                'type' => 'CHAR',
                'constraint' => 36,
                'null' => true,
            ],
            'reviewed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'action_taken' => [
                'type' => 'TEXT',
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
        $this->forge->addKey(['reportable_type', 'reportable_id']);
        $this->forge->addKey(['status', 'created_at']);
        $this->forge->addKey('reporter_id');
        $this->forge->createTable('reports');

        // File attachments table
        $this->forge->addField([
            'id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'attachable_type' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'attachable_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'user_id' => [
                'type' => 'CHAR',
                'constraint' => 36,
            ],
            'original_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
            ],
            'file_path' => [
                'type' => 'VARCHAR',
                'constraint' => 500,
            ],
            'file_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'file_size' => [
                'type' => 'BIGINT',
                'constraint' => 20,
            ],
            'mime_type' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'download_count' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'is_image' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['attachable_type', 'attachable_id']);
        $this->forge->addKey('user_id');
        $this->forge->addKey('file_type');
        $this->forge->createTable('file_attachments');
    }

    public function down()
    {
        $this->forge->dropTable('file_attachments', true);
        $this->forge->dropTable('reports', true);
        $this->forge->dropTable('bookmarks', true);
        $this->forge->dropTable('likes', true);
        $this->forge->dropTable('replies', true);
        $this->forge->dropTable('discussion_views', true);
        $this->forge->dropTable('discussion_tag_pivot', true);
        $this->forge->dropTable('discussions', true);
        $this->forge->dropTable('discussion_tags', true);
        $this->forge->dropTable('forum_moderators', true);
        $this->forge->dropTable('forum_members', true);
        $this->forge->dropTable('forums', true);
    }
}
