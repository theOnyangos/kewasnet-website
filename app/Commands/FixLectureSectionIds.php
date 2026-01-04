<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class FixLectureSectionIds extends BaseCommand
{
    protected $group       = 'Database';
    protected $name        = 'fix:lecture-sections';
    protected $description = 'Fix NULL section_id values in lectures';

    public function run(array $params)
    {
        $db = \Config\Database::connect();
        
        // Get all lectures with NULL section_id
        $lectures = $db->table('course_lectures')
            ->where('section_id', null)
            ->orWhere('section_id', '')
            ->get()
            ->getResultArray();
        
        if (empty($lectures)) {
            CLI::write('No lectures with NULL section_id found.', 'green');
            return;
        }
        
        // Get all sections
        $sections = $db->table('course_sections')
            ->get()
            ->getResultArray();
        
        if (empty($sections)) {
            CLI::write('No sections found!', 'red');
            return;
        }
        
        CLI::write('Found ' . count($lectures) . ' lectures with NULL section_id', 'yellow');
        CLI::write('Found ' . count($sections) . ' sections', 'green');
        CLI::newLine();
        
        // Show available sections
        CLI::write('Available sections:', 'cyan');
        foreach ($sections as $i => $section) {
            CLI::write(($i+1) . '. ' . $section['title'] . ' (ID: ' . $section['id'] . ')');
        }
        CLI::newLine();
        
        // Assign each lecture to a section
        foreach ($lectures as $lecture) {
            CLI::write('Lecture: ' . $lecture['title'], 'yellow');
            CLI::write('Enter section number (1-' . count($sections) . ') or 0 to skip:');
            
            $choice = CLI::prompt('Choice');
            
            if ($choice == '0') {
                CLI::write('Skipped.', 'yellow');
                continue;
            }
            
            $sectionIndex = (int)$choice - 1;
            if (isset($sections[$sectionIndex])) {
                $sectionId = $sections[$sectionIndex]['id'];
                $db->table('course_lectures')
                    ->where('id', $lecture['id'])
                    ->update(['section_id' => $sectionId]);
                    
                CLI::write('✓ Assigned to: ' . $sections[$sectionIndex]['title'], 'green');
            } else {
                CLI::write('Invalid choice, skipped.', 'red');
            }
            
            CLI::newLine();
        }
        
        CLI::write('Done!', 'green');
    }
}
