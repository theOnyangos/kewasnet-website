<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class JobOpportunitiesSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Delete existing data instead of truncate to avoid FK issues
        $db->table('job_opportunities')->emptyTable();

        $opportunities = [
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Senior Water Systems Engineer',
                'slug' => 'senior-water-systems-engineer',
                'description' => 'We are seeking an experienced Water Systems Engineer to lead the design and implementation of water supply systems in rural communities. The role involves technical oversight of borehole construction, pump installation, and water treatment systems. You will work closely with community members and local governments to ensure sustainable water access.',
                'opportunity_type' => 'full-time',
                'location' => 'Nairobi, Kenya',
                'is_remote' => false,
                'salary_min' => 80000.00,
                'salary_max' => 120000.00,
                'salary_currency' => 'KES',
                'application_deadline' => date('Y-m-d', strtotime('+30 days')),
                'status' => 'published',
                'company' => 'KEWASNET',
                'benefits' => json_encode([
                    'Health insurance coverage',
                    'Professional development opportunities',
                    'Field allowances',
                    'Annual leave (21 days)',
                    'Retirement benefits'
                ]),
                'scope' => json_encode([
                    'Design water supply systems',
                    'Supervise construction projects',
                    'Conduct feasibility studies',
                    'Train local technicians'
                ]),
                'document_url' => null,
                'requirements' => json_encode([
                    'Bachelor\'s degree in Civil/Environmental Engineering',
                    '5+ years experience in water systems',
                    'Knowledge of Kenyan water regulations',
                    'Fluency in English and Swahili',
                    'Valid driving license'
                ]),
                'contract_duration' => null,
                'hours_per_week' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => '1c8516a4-5041-4c02-92ca-109857bd0c64',
                'id' => Uuid::uuid4()->toString(),
                'title' => 'WASH Program Coordinator',
                'slug' => 'wash-program-coordinator',
                'description' => 'Join our team as a WASH Program Coordinator to oversee water, sanitation, and hygiene initiatives across multiple counties. This position requires strong project management skills and experience in community engagement. You will coordinate with partners, manage budgets, and ensure program quality and compliance.',
                'opportunity_type' => 'full-time',
                'location' => 'Kisumu, Kenya',
                'is_remote' => false,
                'salary_min' => 65000.00,
                'salary_max' => 95000.00,
                'salary_currency' => 'KES',
                'application_deadline' => date('Y-m-d', strtotime('+25 days')),
                'status' => 'published',
                'company' => 'KEWASNET',
                'benefits' => json_encode([
                    'Comprehensive health cover',
                    'Transport allowance',
                    'Training and certification',
                    'Annual performance bonus',
                    'Flexible working arrangements'
                ]),
                'scope' => json_encode([
                    'Coordinate WASH programs',
                    'Manage partner relationships',
                    'Monitor program outcomes',
                    'Prepare reports and documentation'
                ]),
                'document_url' => null,
                'requirements' => json_encode([
                    'Bachelor\'s degree in Development Studies/Public Health',
                    '3+ years in WASH programming',
                    'Experience with donor reporting',
                    'Strong communication skills',
                    'Ability to work in rural areas'
                ]),
                'contract_duration' => null,
                'hours_per_week' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 'fd2aa8b6-7f05-4759-848d-257203976cbb',
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Community Mobilization Officer',
                'slug' => 'community-mobilization-officer',
                'description' => 'We need a passionate Community Mobilization Officer to work directly with communities to promote water conservation, sanitation practices, and hygiene education. This role involves conducting training sessions, organizing community meetings, and supporting the formation of water user associations.',
                'opportunity_type' => 'full-time',
                'location' => 'Mombasa, Kenya',
                'is_remote' => false,
                'salary_min' => 45000.00,
                'salary_max' => 65000.00,
                'salary_currency' => 'KES',
                'application_deadline' => date('Y-m-d', strtotime('+20 days')),
                'status' => 'published',
                'company' => 'KEWASNET',
                'benefits' => json_encode([
                    'Medical insurance',
                    'Motorbike for field work',
                    'Community engagement training',
                    'Career progression opportunities',
                    'Annual team building'
                ]),
                'scope' => json_encode([
                    'Facilitate community meetings',
                    'Conduct hygiene education',
                    'Support water committee formation',
                    'Document community feedback'
                ]),
                'document_url' => null,
                'requirements' => json_encode([
                    'Diploma in Community Development/Social Work',
                    '2+ years community engagement experience',
                    'Excellent interpersonal skills',
                    'Knowledge of local languages',
                    'Motorcycle riding ability'
                ]),
                'contract_duration' => null,
                'hours_per_week' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 'ea792d50-b0ff-4a2f-93ab-682ab625fd40',
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Water Quality Laboratory Technician',
                'slug' => 'water-quality-lab-technician',
                'description' => 'Seeking a qualified Laboratory Technician to conduct water quality testing and analysis. The role involves collecting water samples, performing chemical and microbiological tests, maintaining laboratory equipment, and preparing detailed test reports for community water sources.',
                'opportunity_type' => 'contract',
                'location' => 'Nakuru, Kenya',
                'is_remote' => false,
                'salary_min' => 35000.00,
                'salary_max' => 50000.00,
                'salary_currency' => 'KES',
                'application_deadline' => date('Y-m-d', strtotime('+15 days')),
                'status' => 'published',
                'company' => 'KEWASNET',
                'benefits' => json_encode([
                    'Laboratory training certification',
                    'Safety equipment provided',
                    'Overtime compensation',
                    'Professional development fund',
                    'Health screening coverage'
                ]),
                'scope' => json_encode([
                    'Perform water quality tests',
                    'Maintain lab equipment',
                    'Collect field samples',
                    'Prepare technical reports'
                ]),
                'document_url' => null,
                'requirements' => json_encode([
                    'Certificate/Diploma in Laboratory Technology',
                    'Experience in water/environmental testing',
                    'Knowledge of WHO water quality standards',
                    'Attention to detail and accuracy',
                    'Computer literacy for data entry'
                ]),
                'contract_duration' => '12 months',
                'hours_per_week' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 'f990fedc-0d2e-4d7d-9002-eb24f3d8af12',
                'id' => Uuid::uuid4()->toString(),
                'title' => 'WASH Training Specialist (Part-Time)',
                'slug' => 'wash-training-specialist-part-time',
                'description' => 'Part-time opportunity for an experienced WASH professional to develop and deliver training programs for local water technicians and community health workers. This role involves curriculum development, training delivery, and capacity building assessments.',
                'opportunity_type' => 'part-time',
                'location' => 'Eldoret, Kenya',
                'is_remote' => true,
                'salary_min' => 25000.00,
                'salary_max' => 40000.00,
                'salary_currency' => 'KES',
                'application_deadline' => date('Y-m-d', strtotime('+35 days')),
                'status' => 'published',
                'company' => 'KEWASNET',
                'benefits' => json_encode([
                    'Flexible schedule',
                    'Remote work option',
                    'Training materials provided',
                    'Professional networking',
                    'Performance incentives'
                ]),
                'scope' => json_encode([
                    'Develop training curricula',
                    'Conduct skills assessments',
                    'Deliver virtual and on-site training',
                    'Monitor training effectiveness'
                ]),
                'document_url' => null,
                'requirements' => json_encode([
                    'Masters in WASH/Public Health/Engineering',
                    '5+ years training experience',
                    'Adult education methodology knowledge',
                    'Excellent presentation skills',
                    'Experience with online learning platforms'
                ]),
                'contract_duration' => null,
                'hours_per_week' => 20,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => '9dded544-f4da-4d25-bbfa-c45360f84e09',
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Environmental Data Analyst Intern',
                'slug' => 'environmental-data-analyst-intern',
                'description' => 'Internship opportunity for a recent graduate or student to gain experience in environmental data analysis and GIS mapping. You will work with water access data, create visualization dashboards, and support research on water security trends across Kenya.',
                'opportunity_type' => 'internship',
                'location' => 'Nairobi, Kenya',
                'is_remote' => true,
                'salary_min' => 15000.00,
                'salary_max' => 25000.00,
                'salary_currency' => 'KES',
                'application_deadline' => date('Y-m-d', strtotime('+40 days')),
                'status' => 'published',
                'company' => 'KEWASNET',
                'benefits' => json_encode([
                    'Mentorship program',
                    'Certificate of completion',
                    'Networking opportunities',
                    'Skills development workshops',
                    'Potential for full-time conversion'
                ]),
                'scope' => json_encode([
                    'Analyze water access data',
                    'Create GIS maps and visualizations',
                    'Support research projects',
                    'Assist with report preparation'
                ]),
                'document_url' => null,
                'requirements' => json_encode([
                    'Bachelor\'s degree in Environmental Science/Statistics/GIS',
                    'Knowledge of R, Python, or STATA',
                    'GIS software experience (QGIS/ArcGIS)',
                    'Strong analytical skills',
                    'Research and writing abilities'
                ]),
                'contract_duration' => '6 months',
                'hours_per_week' => 40,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 'ee366b7c-3b25-4da7-b505-7848db81b069',
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Digital Communications Freelancer',
                'slug' => 'digital-communications-freelancer',
                'description' => 'Freelance opportunity for a creative communications professional to develop digital content for KEWASNET\'s social media, website, and advocacy campaigns. Create compelling stories about water access impact, design infographics, and manage online community engagement.',
                'opportunity_type' => 'freelance',
                'location' => 'Remote',
                'is_remote' => true,
                'salary_min' => 30000.00,
                'salary_max' => 60000.00,
                'salary_currency' => 'KES',
                'application_deadline' => date('Y-m-d', strtotime('+45 days')),
                'status' => 'published',
                'company' => 'KEWASNET',
                'benefits' => json_encode([
                    'Project-based payments',
                    'Creative freedom',
                    'Portfolio development',
                    'Flexible deadlines',
                    'Access to field stories'
                ]),
                'scope' => json_encode([
                    'Create social media content',
                    'Design marketing materials',
                    'Write blog posts and articles',
                    'Manage online communities'
                ]),
                'document_url' => null,
                'requirements' => json_encode([
                    'Degree in Communications/Marketing/Journalism',
                    '3+ years digital marketing experience',
                    'Portfolio of previous work',
                    'Adobe Creative Suite proficiency',
                    'Social media management experience'
                ]),
                'contract_duration' => 'Project-based',
                'hours_per_week' => 15,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 'f232e48c-92d8-4684-bf3a-5ca1a4248eb5',
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Borehole Maintenance Technician',
                'slug' => 'borehole-maintenance-technician',
                'description' => 'Technical role focused on the maintenance and repair of borehole pumps and water systems across rural communities. Requires hands-on experience with pump mechanics, electrical systems, and the ability to work independently in remote locations.',
                'opportunity_type' => 'full-time',
                'location' => 'Machakos, Kenya',
                'is_remote' => false,
                'salary_min' => 40000.00,
                'salary_max' => 60000.00,
                'salary_currency' => 'KES',
                'application_deadline' => date('Y-m-d', strtotime('+28 days')),
                'status' => 'published',
                'company' => 'KEWASNET',
                'benefits' => json_encode([
                    'Technical training programs',
                    'Tools and equipment provided',
                    'Transport and accommodation',
                    'Safety equipment',
                    'Skills certification'
                ]),
                'scope' => json_encode([
                    'Maintain borehole pumps',
                    'Diagnose system problems',
                    'Replace worn components',
                    'Train local technicians'
                ]),
                'document_url' => null,
                'requirements' => json_encode([
                    'Certificate in Mechanical/Electrical Engineering',
                    '2+ years pump maintenance experience',
                    'Knowledge of solar pump systems',
                    'Ability to work in rural areas',
                    'Problem-solving skills'
                ]),
                'contract_duration' => null,
                'hours_per_week' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 'b005ee47-5544-4771-8d69-ca037bcaaa4e',
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Research Assistant - Water Policy',
                'slug' => 'research-assistant-water-policy',
                'description' => 'Research position supporting policy analysis and advocacy work on water governance in Kenya. The role involves literature reviews, stakeholder interviews, policy document analysis, and supporting the development of policy briefs and recommendations.',
                'opportunity_type' => 'contract',
                'location' => 'Nairobi, Kenya',
                'is_remote' => false,
                'salary_min' => 50000.00,
                'salary_max' => 70000.00,
                'salary_currency' => 'KES',
                'application_deadline' => date('Y-m-d', strtotime('+22 days')),
                'status' => 'published',
                'company' => 'KEWASNET',
                'benefits' => json_encode([
                    'Research training',
                    'Conference attendance',
                    'Publication opportunities',
                    'Academic networking',
                    'Reference letters'
                ]),
                'scope' => json_encode([
                    'Conduct policy research',
                    'Interview key stakeholders',
                    'Analyze policy documents',
                    'Draft research reports'
                ]),
                'document_url' => null,
                'requirements' => json_encode([
                    'Masters in Public Policy/Political Science/Law',
                    'Research methodology experience',
                    'Knowledge of Kenyan water policy',
                    'Excellent writing skills',
                    'Interview and analysis skills'
                ]),
                'contract_duration' => '8 months',
                'hours_per_week' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
            [
                'id' => 'fef6a2a0-2988-42a8-8746-181e5a5773a5',
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Financial Management Officer',
                'slug' => 'financial-management-officer',
                'description' => 'We seek a qualified Financial Management Officer to oversee project budgets, financial reporting, and compliance for our WASH programs. This role requires strong analytical skills and experience with donor fund management and financial controls.',
                'opportunity_type' => 'full-time',
                'location' => 'Nairobi, Kenya',
                'is_remote' => false,
                'salary_min' => 70000.00,
                'salary_max' => 100000.00,
                'salary_currency' => 'KES',
                'application_deadline' => date('Y-m-d', strtotime('+30 days')),
                'status' => 'published',
                'company' => 'KEWASNET',
                'benefits' => json_encode([
                    'Professional certification support',
                    'Comprehensive medical cover',
                    'Retirement contribution',
                    'Performance bonuses',
                    'Professional development'
                ]),
                'scope' => json_encode([
                    'Manage project budgets',
                    'Prepare financial reports',
                    'Ensure compliance with donor requirements',
                    'Support audit processes'
                ]),
                'document_url' => null,
                'requirements' => json_encode([
                    'Bachelor\'s degree in Finance/Accounting',
                    'CPA certification preferred',
                    '4+ years NGO financial management',
                    'Experience with donor compliance',
                    'Knowledge of Kenyan financial regulations'
                ]),
                'contract_duration' => null,
                'hours_per_week' => null,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'deleted_at' => null,
            ],
        ];

        // Insert the data
        foreach ($opportunities as $opportunity) {
            $this->db->table('job_opportunities')->insert($opportunity);
        }

        echo "✅ JobOpportunitiesSeeder completed successfully! 10 job opportunities created.\n";
    }
}
