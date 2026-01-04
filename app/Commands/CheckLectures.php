<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class CheckLectures extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'check:lectures';
    protected $description = 'Check lectures and sections status';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        CLI::write("=== Course Lectures ===\n", 'green');
        $lectures = $db->table('course_lectures')
            ->select('id, section_id, title, deleted_at')
            ->get()
            ->getResultArray();
        
        foreach($lectures as $lecture) {
            CLI::write("ID: " . $lecture['id']);
            CLI::write("Section ID: " . ($lecture['section_id'] ?: 'NULL'));
            CLI::write("Title: " . $lecture['title']);
            CLI::write("Deleted: " . ($lecture['deleted_at'] ?: 'NO'));
            CLI::write("---\n");
        }
        
        CLI::write("\n=== Course Sections ===\n", 'green');
        $sections = $db->table('course_sections')
            ->select('id, title, course_id')
            ->get()
            ->getResultArray();
        
        foreach($sections as $section) {
            CLI::write("ID: " . $section['id']);
            CLI::write("Title: " . $section['title']);
            CLI::write("Course ID: " . $section['course_id']);
            CLI::write("---\n");
        }
    }
}
