<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use Config\Database;

class FixDiscussionPaths extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'fix:discussion-paths';
    protected $description = 'Fix discussion attachment file paths by removing uploads/ prefix';

    public function run(array $params)
    {
        $db = Database::connect();
        
        // Get all discussion attachments with 'uploads/' in the path
        $builder = $db->table('file_attachments');
        $builder->where('attachable_type', 'discussion');
        $builder->like('file_path', 'uploads/', 'after');
        $attachments = $builder->get()->getResultArray();

        CLI::write("Found " . count($attachments) . " discussion attachments with 'uploads/' prefix\n", 'yellow');

        if (count($attachments) === 0) {
            CLI::write("No attachments to fix!", 'green');
            return;
        }

        foreach ($attachments as $attachment) {
            $oldPath = $attachment['file_path'];
            // Remove 'uploads/' prefix
            $newPath = str_replace('uploads/discussions/', 'discussions/', $oldPath);
            
            CLI::write("ID: {$attachment['id']}", 'light_gray');
            CLI::write("  Old: $oldPath", 'red');
            CLI::write("  New: $newPath", 'green');
            
            // Update the record
            $db->table('file_attachments')
               ->where('id', $attachment['id'])
               ->update(['file_path' => $newPath]);
            
            CLI::write("  ✓ Updated\n", 'green');
        }

        CLI::write("Migration complete!", 'green');
    }
}
