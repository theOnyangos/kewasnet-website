<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\Program;
use Ramsey\Uuid\Uuid;

class ProgramSeeder extends Seeder
{
    public function run()
    {
        $programModel = new Program();

        $programs = [
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Water Security Initiative',
                'slug' => 'water-security-initiative',
                'description' => 'Developing sustainable water infrastructure and management systems to ensure reliable access to clean water in rural and urban communities.',
                'content' => '<h2>Water Security Initiative</h2><p>Our Water Security Initiative focuses on creating sustainable water solutions that ensure communities have reliable access to clean, safe water. Through innovative infrastructure development and community engagement, we work to address water scarcity challenges across Kenya.</p><h3>Key Components</h3><ul><li>Borehole drilling and rehabilitation</li><li>Water quality testing and monitoring</li><li>Community-based water management training</li><li>Solar-powered water systems</li><li>Water storage solutions</li></ul>',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 7.172V5L8 4z"></path>',
                'background_color' => 'bg-primary',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
                'meta_title' => 'Water Security Initiative - KEWASNET',
                'meta_description' => 'Learn about our Water Security Initiative focused on sustainable water infrastructure and management systems for communities.',
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Sanitation Excellence Program',
                'slug' => 'sanitation-excellence-program',
                'description' => 'Promoting dignified sanitation solutions through improved infrastructure, behavior change, and policy advocacy for communities across Kenya.',
                'content' => '<h2>Sanitation Excellence Program</h2><p>The Sanitation Excellence Program aims to improve sanitation standards through comprehensive infrastructure development, community education, and policy advocacy. We believe that access to dignified sanitation is a basic human right.</p><h3>Program Objectives</h3><ul><li>Construction of improved sanitation facilities</li><li>Community-led total sanitation (CLTS)</li><li>Waste management systems</li><li>Sanitation policy advocacy</li><li>Public toilet management</li></ul>',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>',
                'background_color' => 'bg-secondary',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 2,
                'meta_title' => 'Sanitation Excellence Program - KEWASNET',
                'meta_description' => 'Discover our Sanitation Excellence Program promoting dignified sanitation solutions and improved infrastructure.',
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Hygiene Education Campaign',
                'slug' => 'hygiene-education-campaign',
                'description' => 'Comprehensive hygiene education and behavior change programs targeting schools, healthcare facilities, and community centers.',
                'content' => '<h2>Hygiene Education Campaign</h2><p>Our Hygiene Education Campaign promotes good hygiene practices through comprehensive education programs and behavior change initiatives. We target schools, healthcare facilities, and community centers to maximize impact.</p><h3>Campaign Focus Areas</h3><ul><li>Hand hygiene promotion</li><li>Menstrual hygiene management</li><li>Food safety and hygiene</li><li>Environmental hygiene</li><li>Personal hygiene education</li></ul>',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>',
                'background_color' => 'bg-secondaryShades-500',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 3,
                'meta_title' => 'Hygiene Education Campaign - KEWASNET',
                'meta_description' => 'Join our Hygiene Education Campaign promoting good hygiene practices in schools and communities.',
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Climate Resilience Program',
                'slug' => 'climate-resilience-program',
                'description' => 'Building climate-adaptive WASH systems and promoting sustainable practices to address the impacts of climate change on water resources.',
                'content' => '<h2>Climate Resilience Program</h2><p>The Climate Resilience Program addresses the growing challenges of climate change on water, sanitation, and hygiene systems. We develop adaptive strategies and resilient infrastructure to ensure continued service delivery.</p><h3>Resilience Strategies</h3><ul><li>Climate-smart water systems</li><li>Drought preparedness planning</li><li>Flood-resistant infrastructure</li><li>Rainwater harvesting systems</li><li>Climate adaptation training</li></ul>',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>',
                'background_color' => 'bg-primary',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 4,
                'meta_title' => 'Climate Resilience Program - KEWASNET',
                'meta_description' => 'Learn about our Climate Resilience Program building adaptive WASH systems for climate change.',
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Youth Engagement Initiative',
                'slug' => 'youth-engagement-initiative',
                'description' => 'Empowering young leaders through capacity building, mentorship, and leadership opportunities in the WASH sector.',
                'content' => '<h2>Youth Engagement Initiative</h2><p>The Youth Engagement Initiative empowers young people to become leaders in the WASH sector through comprehensive capacity building, mentorship programs, and practical leadership opportunities.</p><h3>Youth Programs</h3><ul><li>Leadership development training</li><li>Mentorship programs</li><li>Youth internship opportunities</li><li>School WASH clubs</li><li>Youth innovation challenges</li></ul>',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>',
                'background_color' => 'bg-secondary',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 5,
                'meta_title' => 'Youth Engagement Initiative - KEWASNET',
                'meta_description' => 'Discover our Youth Engagement Initiative empowering young leaders in the WASH sector.',
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Policy Advocacy Program',
                'slug' => 'policy-advocacy-program',
                'description' => 'Influencing policy development and implementation to create an enabling environment for sustainable WASH service delivery.',
                'content' => '<h2>Policy Advocacy Program</h2><p>Our Policy Advocacy Program works to influence policy development and implementation at local, national, and regional levels to create an enabling environment for sustainable WASH service delivery.</p><h3>Advocacy Areas</h3><ul><li>WASH policy development</li><li>Regulatory framework improvement</li><li>Budget allocation advocacy</li><li>Community participation policies</li><li>Gender-inclusive WASH policies</li></ul>',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>',
                'background_color' => 'bg-secondaryShades-500',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 6,
                'meta_title' => 'Policy Advocacy Program - KEWASNET',
                'meta_description' => 'Learn about our Policy Advocacy Program influencing WASH policy development and implementation.',
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Community Engagement Program',
                'slug' => 'community-engagement-program',
                'description' => 'Building strong partnerships with communities to ensure sustainable WASH solutions through participatory approaches and local ownership.',
                'content' => '<h2>Community Engagement Program</h2><p>The Community Engagement Program focuses on building strong partnerships with communities to ensure sustainable WASH solutions through participatory approaches and fostering local ownership.</p><h3>Engagement Strategies</h3><ul><li>Community mobilization</li><li>Participatory planning</li><li>Local capacity building</li><li>Community ownership development</li><li>Traditional leader engagement</li></ul>',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>',
                'background_color' => 'bg-primary',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 7,
                'meta_title' => 'Community Engagement Program - KEWASNET',
                'meta_description' => 'Discover our Community Engagement Program building partnerships for sustainable WASH solutions.',
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Innovation & Technology Hub',
                'slug' => 'innovation-technology-hub',
                'description' => 'Promoting innovative technologies and solutions for enhanced WASH service delivery through research, development, and pilot projects.',
                'content' => '<h2>Innovation & Technology Hub</h2><p>The Innovation & Technology Hub promotes cutting-edge technologies and innovative solutions for enhanced WASH service delivery through research, development, and pilot projects.</p><h3>Innovation Areas</h3><ul><li>Smart water monitoring systems</li><li>Mobile payment solutions</li><li>Water quality testing technology</li><li>Renewable energy applications</li><li>Digital platform development</li></ul>',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>',
                'background_color' => 'bg-secondary',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 8,
                'meta_title' => 'Innovation & Technology Hub - KEWASNET',
                'meta_description' => 'Explore our Innovation & Technology Hub promoting innovative WASH solutions and technologies.',
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'title' => 'Emergency WASH Response',
                'slug' => 'emergency-wash-response',
                'description' => 'Providing rapid WASH response during emergencies and disasters to ensure continued access to essential services for affected communities.',
                'content' => '<h2>Emergency WASH Response</h2><p>Our Emergency WASH Response program provides rapid and effective WASH interventions during emergencies and disasters to ensure continued access to essential water, sanitation, and hygiene services for affected communities.</p><h3>Response Capabilities</h3><ul><li>Rapid assessment teams</li><li>Emergency water provision</li><li>Temporary sanitation facilities</li><li>Hygiene kit distribution</li><li>Emergency coordination</li></ul>',
                'icon_svg' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>',
                'background_color' => 'bg-red-500',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 9,
                'meta_title' => 'Emergency WASH Response - KEWASNET',
                'meta_description' => 'Learn about our Emergency WASH Response providing rapid interventions during disasters and emergencies.',
            ],
        ];

        foreach ($programs as $program) {
            $programModel->insert($program);
        }
    }
}
