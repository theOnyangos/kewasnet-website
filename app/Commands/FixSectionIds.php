<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixSectionIds extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'fix:section-ids';
    protected $description = 'Fix empty section IDs by generating UUIDs';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        $sections = $db->table('course_sections')
            ->get()
            ->getResultArray();
        
        CLI::write("Found " . count($sections) . " sections", 'green');
        
        $fixedCount = 0;
        foreach($sections as $section) {
            if (empty($section['id'])) {
                $newId = sprintf(
                    '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                    mt_rand(0, 0xffff), mt_rand(0, 0xffff),
                    mt_rand(0, 0xffff),
                    mt_rand(0, 0x0fff) | 0x4000,
                    mt_rand(0, 0x3fff) | 0x8000,
                    mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
                );
                
                CLI::write("Fixing section '" . $section['title'] . "' with new ID: " . $newId, 'yellow');
                
                // Update the section with a new UUID
                $db->table('course_sections')
                    ->where('title', $section['title'])
                    ->where('course_id', $section['course_id'])
                    ->where('created_at', $section['created_at'])
                    ->update(['id' => $newId]);
                
                $fixedCount++;
            } else {
                CLI::write("Section '" . $section['title'] . "' has ID: " . $section['id'], 'green');
            }
        }
        
        CLI::write("\nFixed $fixedCount sections!", 'green');
    }
}
