<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class JobApplicationsSeeder extends Seeder
{
    public function run()
    {
        // Get existing job opportunities
        $opportunityModel = new \App\Models\JobOpportunityModel();
        $opportunities = $opportunityModel->findAll();

        if (empty($opportunities)) {
            echo "No job opportunities found. Please run JobOpportunitiesSeeder first.\n";
            return;
        }
        
        // Sample applicants data
        $applicants = [
            [
                'first_name' => 'Alice',
                'last_name' => 'Wanjiku',
                'email' => 'alice.wanjiku@gmail.com',
                'phone' => '+254701234567',
                'current_job_title' => 'Water Engineer',
                'current_company' => 'Kenya Water Institute',
                'years_of_experience' => 5,
                'education_level' => 'bachelor',
                'skills' => ['Water treatment', 'Project management', 'AutoCAD', 'GIS mapping'],
                'linkedin_profile' => 'https://linkedin.com/in/alice-wanjiku',
                'portfolio_url' => 'https://alicewanjiku.portfolio.com',
                'status' => 'pending'
            ],
            [
                'id' => '4590c562-85f1-4f9a-bcff-cdcdf32f6804',
                'first_name' => 'John',
                'last_name' => 'Mwangi',
                'email' => 'john.mwangi@yahoo.com',
                'phone' => '+254722345678',
                'current_job_title' => 'Environmental Scientist',
                'current_company' => 'Green Solutions Ltd',
                'years_of_experience' => 3,
                'education_level' => 'master',
                'skills' => ['Environmental assessment', 'Data analysis', 'Research methodology', 'Report writing'],
                'linkedin_profile' => 'https://linkedin.com/in/john-mwangi',
                'status' => 'reviewed'
            ],
            [
                'id' => '344b8883-6fa1-41b9-8e76-f2b459050130',
                'first_name' => 'Grace',
                'last_name' => 'Nyong\'o',
                'email' => 'grace.nyongo@outlook.com',
                'phone' => '+254733456789',
                'current_job_title' => 'Community Development Officer',
                'current_company' => 'USAID Kenya',
                'years_of_experience' => 7,
                'education_level' => 'master',
                'skills' => ['Community mobilization', 'Project coordination', 'Stakeholder engagement', 'Training facilitation'],
                'linkedin_profile' => 'https://linkedin.com/in/grace-nyongo',
                'status' => 'interviewed'
            ],
            [
                'id' => '018d9ada-c1d7-4fb7-8d7b-8afd7e94d43b',
                'first_name' => 'David',
                'last_name' => 'Kiprotich',
                'email' => 'david.kiprotich@gmail.com',
                'phone' => '+254744567890',
                'current_job_title' => 'Financial Analyst',
                'current_company' => 'Equity Bank',
                'years_of_experience' => 4,
                'education_level' => 'bachelor',
                'skills' => ['Financial modeling', 'Budget management', 'Excel expertise', 'Report generation'],
                'linkedin_profile' => 'https://linkedin.com/in/david-kiprotich',
                'status' => 'pending'
            ],
            [
                'id' => '93038417-6401-4175-ae06-398f6513cfbe',
                'first_name' => 'Mary',
                'last_name' => 'Akinyi',
                'email' => 'mary.akinyi@gmail.com',
                'phone' => '+254755678901',
                'current_job_title' => 'Fresh Graduate',
                'current_company' => '',
                'years_of_experience' => 0,
                'education_level' => 'bachelor',
                'skills' => ['Microsoft Office', 'Communication', 'Teamwork', 'Problem solving'],
                'status' => 'pending'
            ],
            [
                'id' => '73c548bd-4de9-4921-9441-6ba0795b17e2',
                'first_name' => 'Samuel',
                'last_name' => 'Mutua',
                'email' => 'samuel.mutua@hotmail.com',
                'phone' => '+254766789012',
                'current_job_title' => 'Water Technician',
                'current_company' => 'Nakuru Water Company',
                'years_of_experience' => 8,
                'education_level' => 'associate',
                'skills' => ['Borehole maintenance', 'Pump repair', 'Water quality testing', 'Technical documentation'],
                'linkedin_profile' => 'https://linkedin.com/in/samuel-mutua',
                'status' => 'reviewed'
            ],
            [
                'id' => 'de621cad-8694-4fbb-9589-53a4ae3fd7ae',
                'first_name' => 'Faith',
                'last_name' => 'Wambui',
                'email' => 'faith.wambui@gmail.com',
                'phone' => '+254777890123',
                'current_job_title' => 'Research Assistant',
                'current_company' => 'University of Nairobi',
                'years_of_experience' => 2,
                'education_level' => 'master',
                'skills' => ['Research methodology', 'Data collection', 'Statistical analysis', 'Academic writing'],
                'linkedin_profile' => 'https://linkedin.com/in/faith-wambui',
                'status' => 'interviewed'
            ],
            [
                'id' => 'd5cfa00d-3d37-432a-9a2a-d72bc26e8a54',
                'first_name' => 'Peter',
                'last_name' => 'Otieno',
                'email' => 'peter.otieno@gmail.com',
                'phone' => '+254788901234',
                'current_job_title' => 'Marketing Coordinator',
                'current_company' => 'Safaricom',
                'years_of_experience' => 6,
                'education_level' => 'bachelor',
                'skills' => ['Digital marketing', 'Social media management', 'Content creation', 'Campaign management'],
                'linkedin_profile' => 'https://linkedin.com/in/peter-otieno',
                'portfolio_url' => 'https://peterotieno.works',
                'status' => 'pending'
            ],
            [
                'id' => 'a75a13d6-a38d-4ce3-93be-d73a9a6bd8a7',
                'first_name' => 'Esther',
                'last_name' => 'Koech',
                'email' => 'esther.koech@yahoo.com',
                'phone' => '+254799012345',
                'current_job_title' => 'Lab Technician',
                'current_company' => 'Kenya Medical Research Institute',
                'years_of_experience' => 4,
                'education_level' => 'associate',
                'skills' => ['Laboratory procedures', 'Quality control', 'Equipment maintenance', 'Data recording'],
                'status' => 'rejected'
            ],
            [
                'id' => 'e8ebc9fb-c310-4016-88c4-a57a8656606d',
                'first_name' => 'Michael',
                'last_name' => 'Kamau',
                'email' => 'michael.kamau@gmail.com',
                'phone' => '+254700123456',
                'current_job_title' => 'Civil Engineer',
                'current_company' => 'Kenya National Highways Authority',
                'years_of_experience' => 10,
                'education_level' => 'master',
                'skills' => ['Structural design', 'Project management', 'AutoCAD', 'Construction supervision'],
                'linkedin_profile' => 'https://linkedin.com/in/michael-kamau',
                'status' => 'hired'
            ],
            [
                'id' => 'bd48da75-7341-4083-96bb-8c8a4bb65175',
                'first_name' => 'Nancy',
                'last_name' => 'Cherop',
                'email' => 'nancy.cherop@outlook.com',
                'phone' => '+254711234567',
                'current_job_title' => 'Communications Specialist',
                'current_company' => 'World Vision Kenya',
                'years_of_experience' => 5,
                'education_level' => 'bachelor',
                'skills' => ['Content writing', 'Public relations', 'Event management', 'Media relations'],
                'linkedin_profile' => 'https://linkedin.com/in/nancy-cherop',
                'status' => 'pending'
            ],
            [
                'id' => '0fb4d641-03f8-4e21-be54-214db19a277e',
                'first_name' => 'Robert',
                'last_name' => 'Njoroge',
                'email' => 'robert.njoroge@gmail.com',
                'phone' => '+254722567890',
                'current_job_title' => 'System Administrator',
                'current_company' => 'Kenya Commercial Bank',
                'years_of_experience' => 6,
                'education_level' => 'bachelor',
                'skills' => ['Network management', 'Database administration', 'System security', 'Technical support'],
                'status' => 'reviewed'
            ],
            [
                'id' => '36c0e9e2-0f1a-4ac8-a3df-bb3a11713ca2',
                'first_name' => 'Catherine',
                'last_name' => 'Wairimu',
                'email' => 'catherine.wairimu@yahoo.com',
                'phone' => '+254733890234',
                'current_job_title' => 'Intern',
                'current_company' => 'Ministry of Water',
                'years_of_experience' => 1,
                'education_level' => 'bachelor',
                'skills' => ['Microsoft Office', 'Research skills', 'Report writing', 'Data entry'],
                'status' => 'reviewed'
            ],
            [
                'id' => '21994a00-8cda-4c44-8c9a-60e3a4220412',
                'first_name' => 'James',
                'last_name' => 'Wekesa',
                'email' => 'james.wekesa@gmail.com',
                'phone' => '+254744123789',
                'current_job_title' => 'Finance Manager',
                'current_company' => 'Co-operative Bank',
                'years_of_experience' => 8,
                'education_level' => 'master',
                'skills' => ['Financial planning', 'Risk management', 'Team leadership', 'Strategic planning'],
                'linkedin_profile' => 'https://linkedin.com/in/james-wekesa',
                'status' => 'interviewed'
            ],
            [
                'id' => 'c3aa40a5-29c8-48aa-9dc2-22ddd851b0fb',
                'first_name' => 'Linda',
                'last_name' => 'Moraa',
                'email' => 'linda.moraa@outlook.com',
                'phone' => '+254755345678',
                'current_job_title' => 'Program Officer',
                'current_company' => 'Oxfam Kenya',
                'years_of_experience' => 4,
                'education_level' => 'master',
                'skills' => ['Program management', 'Monitoring and evaluation', 'Donor relations', 'Capacity building'],
                'linkedin_profile' => 'https://linkedin.com/in/linda-moraa',
                'status' => 'pending'
            ]
        ];
        
        // Generate applications for each opportunity
        $jobApplicationsModel = new \App\Models\JobApplicantModel();
        $applications = [];
        $usedEmails = []; // Track used emails to avoid duplicates
        
        // Ensure each opportunity gets some applications
        foreach ($opportunities as $index => $opportunity) {
            // Each opportunity gets 1-4 random applicants
            $numApplications = rand(1, 4);
            $shuffledApplicants = $applicants;
            shuffle($shuffledApplicants);
            
            $applicationCount = 0;
            foreach ($shuffledApplicants as $applicant) {
                if ($applicationCount >= $numApplications) break;
                
                // Create unique email to avoid duplicates for the same opportunity
                $uniqueEmail = $applicant['email'];
                $emailKey = $opportunity['id'] . '_' . $uniqueEmail;
                
                if (in_array($emailKey, $usedEmails)) {
                    continue; // Skip if this person already applied to this opportunity
                }
                
                $usedEmails[] = $emailKey;
                    
                // Create unique application data
                $applicationData = [
                    'id' => Uuid::uuid4()->toString(),
                    'opportunity_id' => $opportunity['id'],
                    'first_name' => $applicant['first_name'],
                    'last_name' => $applicant['last_name'],
                    'email' => $applicant['email'],
                    'phone' => $applicant['phone'],
                    'resume_path' => '/uploads/resumes/' . strtolower($applicant['first_name'] . '_' . $applicant['last_name']) . '_resume.pdf',
                    'cover_letter_path' => '/uploads/cover_letters/' . strtolower($applicant['first_name'] . '_' . $applicant['last_name']) . '_cover_letter.pdf',
                    'linkedin_profile' => $applicant['linkedin_profile'] ?? null,
                    'portfolio_url' => $applicant['portfolio_url'] ?? null,
                    'current_job_title' => $applicant['current_job_title'],
                    'current_company' => $applicant['current_company'] ?: null,
                    'years_of_experience' => $applicant['years_of_experience'],
                    'education_level' => $applicant['education_level'],
                    'skills' => json_encode($applicant['skills']),
                    'status' => $applicant['status'],
                    'notes' => $this->generateNotes($applicant['status']),
                    'custom_fields' => json_encode([
                        'salary_expectation' => rand(30000, 120000),
                        'availability' => rand(1, 4) . ' weeks notice',
                        'willing_to_relocate' => rand(0, 1) ? 'Yes' : 'No'
                    ]),
                    'ip_address' => $this->generateRandomIP(),
                    'user_agent' => $this->generateRandomUserAgent(),
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
                    'updated_at' => date('Y-m-d H:i:s', strtotime('-' . rand(0, 7) . ' days'))
                ];
                
                $applications[] = $applicationData;
                $applicationCount++;
            }
        }
        
        // Add some additional random applications
        for ($i = 0; $i < 15; $i++) {
            $randomOpportunity = $opportunities[array_rand($opportunities)];
            $randomApplicant = $applicants[array_rand($applicants)];
            
            // Check for duplicates
            $emailKey = $randomOpportunity['id'] . '_' . $randomApplicant['email'];
            if (in_array($emailKey, $usedEmails)) {
                continue;
            }
            $usedEmails[] = $emailKey;
            
            $applicationData = [
                'id' => Uuid::uuid4()->toString(),
                'opportunity_id' => $randomOpportunity['id'],
                'first_name' => $randomApplicant['first_name'],
                'last_name' => $randomApplicant['last_name'],
                'email' => $randomApplicant['email'],
                'phone' => $randomApplicant['phone'],
                'resume_path' => '/uploads/resumes/' . strtolower($randomApplicant['first_name'] . '_' . $randomApplicant['last_name']) . '_resume.pdf',
                'cover_letter_path' => '/uploads/cover_letters/' . strtolower($randomApplicant['first_name'] . '_' . $randomApplicant['last_name']) . '_cover_letter.pdf',
                'linkedin_profile' => $randomApplicant['linkedin_profile'] ?? null,
                'portfolio_url' => $randomApplicant['portfolio_url'] ?? null,
                'current_job_title' => $randomApplicant['current_job_title'],
                'current_company' => $randomApplicant['current_company'] ?: null,
                'years_of_experience' => $randomApplicant['years_of_experience'],
                'education_level' => $randomApplicant['education_level'],
                'skills' => json_encode($randomApplicant['skills']),
                'status' => $randomApplicant['status'],
                'notes' => $this->generateNotes($randomApplicant['status']),
                'custom_fields' => json_encode([
                    'salary_expectation' => rand(30000, 120000),
                    'availability' => rand(1, 4) . ' weeks notice',
                    'willing_to_relocate' => rand(0, 1) ? 'Yes' : 'No'
                ]),
                'ip_address' => $this->generateRandomIP(),
                'user_agent' => $this->generateRandomUserAgent(),
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 60) . ' days')),
                'updated_at' => date('Y-m-d H:i:s', strtotime('-' . rand(0, 14) . ' days'))
            ];
            
            $applications[] = $applicationData;
        }

        // Insert all applications
        $jobApplicationsModel->insertBatch($applications);
        
        echo "Created " . count($applications) . " job applications successfully!\n";
    }
    
    private function generateNotes($status)
    {
        $notes = [
            'pending' => [
                'Application received and under initial review.',
                'Waiting for complete documentation.',
                'Application submitted successfully.'
            ],
            'reviewed' => [
                'Initial screening completed. Good qualifications.',
                'Resume reviewed. Meets basic requirements.',
                'Background check in progress.',
                'Selected for next round of interviews.',
                'Impressive qualifications and experience.'
            ],
            'interviewed' => [
                'Interview conducted. Awaiting final decision.',
                'Technical interview completed successfully.',
                'Second round interview scheduled.',
                'Top candidate for this position.'
            ],
            'rejected' => [
                'Thank you for your application. Position filled.',
                'Qualifications do not match current requirements.',
                'We will keep your profile for future opportunities.'
            ],
            'hired' => [
                'Congratulations! Offer letter sent.',
                'Selected candidate. Contract preparation in progress.',
                'Background verification completed. Ready to onboard.'
            ]
        ];
        
        return $notes[$status][array_rand($notes[$status])];
    }
    
    private function generateRandomIP()
    {
        return rand(41, 197) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(1, 254);
    }
    
    private function generateRandomUserAgent()
    {
        $userAgents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.1.1 Safari/605.1.15',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (iPad; CPU OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0 Firefox/88.0'
        ];
        
        return $userAgents[array_rand($userAgents)];
    }
}
