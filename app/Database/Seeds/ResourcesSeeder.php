<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Test\Fabricator;

class ResourcesSeeder extends Seeder
{
    public function run()
    {
        // Get existing pillars, categories, and document types
        $pillars = $this->db->table('pillars')->get()->getResultArray();
        $categories = $this->db->table('resource_categories')->get()->getResultArray();
        $documentTypes = $this->db->table('document_types')->get()->getResultArray();

        if (empty($pillars) || empty($categories) || empty($documentTypes)) {
            echo "Please ensure pillars, resource_categories, and document_types tables are populated first.\n";
            return;
        }

        // Sample resources data
        $resources = [
            [
                'title' => 'Kenya WASH Policy Framework 2024',
                'slug' => 'kenya-wash-policy-framework-2024',
                'summary' => 'Comprehensive analysis of Kenya\'s updated WASH policy framework for achieving universal access by 2030.',
                'description' => 'This comprehensive policy brief provides an in-depth analysis of Kenya\'s updated Water, Sanitation, and Hygiene (WASH) policy framework. The document outlines key changes, implementation strategies, and roadmap for achieving universal access to safe water, adequate sanitation, and hygiene services by 2030. It includes detailed recommendations for policymakers, implementation partners, and development agencies working in the WASH sector.',
                'file_path' => 'policy-briefs/kenya-wash-policy-framework-2024.pdf',
                'file_size' => 2457600, // 2.4 MB
                'download_count' => 1247,
                'view_count' => 3891,
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'title' => 'Rural Sanitation Impact Assessment Report',
                'slug' => 'rural-sanitation-impact-assessment-2024',
                'summary' => 'Evaluation of rural sanitation interventions across 15 counties measuring health outcomes and behavioral changes.',
                'description' => 'This comprehensive research report presents findings from a multi-county evaluation of rural sanitation interventions implemented across 15 counties in Kenya. The study measures health outcomes, behavioral changes, and economic impacts of various sanitation approaches including Community-Led Total Sanitation (CLTS), household latrines, and institutional sanitation facilities. The report provides evidence-based recommendations for scaling successful interventions.',
                'file_path' => 'research-reports/rural-sanitation-impact-assessment-2024.pdf',
                'file_size' => 4300800, // 4.1 MB
                'download_count' => 892,
                'view_count' => 2156,
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'title' => 'Community Hygiene Training Manual',
                'slug' => 'community-hygiene-training-manual-2024',
                'summary' => 'Comprehensive training manual for community health workers and hygiene promoters with session plans and visual aids.',
                'description' => 'This comprehensive training manual is designed for community health workers, hygiene promoters, and community volunteers involved in hygiene education and behavior change programs. The manual includes detailed session plans, participatory learning activities, visual aids, and monitoring tools. It covers key hygiene practices including handwashing, food hygiene, menstrual hygiene management, and household water treatment and safe storage.',
                'file_path' => 'training-materials/community-hygiene-training-manual-2024.pdf',
                'file_size' => 7024640, // 6.7 MB
                'download_count' => 1834,
                'view_count' => 4627,
                'is_featured' => 1,
                'status' => 'published'
            ],
            [
                'title' => 'Water Quality in Urban Slums Study',
                'slug' => 'water-quality-urban-slums-study-2023',
                'summary' => 'Study on water quality challenges and solutions in Nairobi\'s informal settlements.',
                'description' => 'This research study examines water quality challenges in Nairobi\'s informal settlements, analyzing microbial contamination, chemical pollutants, and household water treatment practices. The study presents innovative solutions for improving water quality access and treatment in urban slum environments.',
                'file_path' => 'research-reports/water-quality-urban-slums-2023.pdf',
                'file_size' => 5452800, // 5.2 MB
                'download_count' => 1203,
                'view_count' => 2847,
                'is_featured' => 0,
                'status' => 'published'
            ],
            [
                'title' => 'WASH in Schools Implementation Guidelines',
                'slug' => 'wash-schools-implementation-guidelines-2023',
                'summary' => 'Technical guidelines for implementing WASH programs in primary and secondary schools.',
                'description' => 'These technical guidelines provide comprehensive instructions for implementing Water, Sanitation, and Hygiene programs in educational institutions. The document covers infrastructure requirements, hygiene education curricula, maintenance protocols, and monitoring frameworks for sustainable WASH services in schools.',
                'file_path' => 'guidelines/wash-schools-implementation-guidelines-2023.pdf',
                'file_size' => 6082560, // 5.8 MB
                'download_count' => 1426,
                'view_count' => 3214,
                'is_featured' => 0,
                'status' => 'published'
            ],
            [
                'title' => 'Community-Led Total Sanitation Best Practices',
                'slug' => 'clts-best-practices-western-kenya-2024',
                'summary' => 'Case studies of successful CLTS implementations in Western Kenya with replication strategies.',
                'description' => 'This best practices document presents successful Community-Led Total Sanitation implementations in Western Kenya. It includes detailed case studies, lessons learned, and replication strategies for achieving open defecation free communities through community-driven approaches.',
                'file_path' => 'best-practices/clts-best-practices-western-kenya-2024.pdf',
                'file_size' => 3883008, // 3.7 MB
                'download_count' => 1532,
                'view_count' => 2891,
                'is_featured' => 0,
                'status' => 'published'
            ],
            [
                'title' => 'Rainwater Harvesting Systems Manual',
                'slug' => 'rainwater-harvesting-systems-arid-regions-2023',
                'summary' => 'Documentation of effective rainwater harvesting models for arid and semi-arid regions.',
                'description' => 'This technical manual documents effective rainwater harvesting models specifically designed for arid and semi-arid regions. It includes design specifications, construction guidelines, maintenance requirements, and cost-benefit analyses for various rainwater harvesting technologies.',
                'file_path' => 'best-practices/rainwater-harvesting-systems-2023.pdf',
                'file_size' => 4718592, // 4.5 MB
                'download_count' => 2104,
                'view_count' => 4873,
                'is_featured' => 0,
                'status' => 'published'
            ],
            [
                'title' => 'WASH Project Monitoring Toolkit',
                'slug' => 'wash-project-monitoring-toolkit-2024',
                'summary' => 'Comprehensive templates and tools for monitoring WASH project implementation and outcomes.',
                'description' => 'This comprehensive toolkit provides templates, indicators, and tools for monitoring Water, Sanitation, and Hygiene projects throughout the project cycle. It includes result frameworks, data collection tools, reporting templates, and evaluation guidelines for measuring project outcomes and impacts.',
                'file_path' => 'tools-templates/wash-project-monitoring-toolkit-2024.pdf',
                'file_size' => 4087808, // 3.9 MB
                'download_count' => 987,
                'view_count' => 1872,
                'is_featured' => 0,
                'status' => 'published'
            ],
            [
                'title' => 'Menstrual Hygiene Management Guidelines',
                'slug' => 'menstrual-hygiene-management-guidelines-2024',
                'summary' => 'Guidelines for implementing menstrual hygiene management programs in schools and communities.',
                'description' => 'These guidelines provide comprehensive instructions for implementing menstrual hygiene management programs in schools and communities. The document covers infrastructure requirements, education materials, supply chain management, and community engagement strategies for supporting girls and women.',
                'file_path' => 'guidelines/menstrual-hygiene-management-guidelines-2024.pdf',
                'file_size' => 3356672, // 3.2 MB
                'download_count' => 1675,
                'view_count' => 3892,
                'is_featured' => 0,
                'status' => 'published'
            ],
            [
                'title' => 'Climate Resilient WASH Systems Report',
                'slug' => 'climate-resilient-wash-systems-2024',
                'summary' => 'Analysis of climate-resilient WASH technologies and adaptation strategies for Kenya.',
                'description' => 'This report analyzes climate-resilient Water, Sanitation, and Hygiene technologies and adaptation strategies suitable for Kenya\'s diverse climate zones. It presents innovative approaches for ensuring sustainable WASH services under changing climate conditions and extreme weather events.',
                'file_path' => 'research-reports/climate-resilient-wash-systems-2024.pdf',
                'file_size' => 5767168, // 5.5 MB
                'download_count' => 743,
                'view_count' => 1654,
                'is_featured' => 0,
                'status' => 'published'
            ]
        ];

        // Insert resources with random category and document type assignments
        foreach ($resources as $resource) {
            // Randomly assign pillar, category, and document type
            $randomPillar = $pillars[array_rand($pillars)];
            $randomCategory = $categories[array_rand($categories)];
            $randomDocumentType = $documentTypes[array_rand($documentTypes)];

            $resourceData = array_merge($resource, [
                'id' => $this->generateUUID(),
                'pillar_id' => $randomPillar['id'],
                'category_id' => $randomCategory['id'],
                'document_type_id' => $randomDocumentType['id'],
                'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days')),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $this->db->table('resources')->insert($resourceData);
        }

        echo "Seeded " . count($resources) . " resources successfully.\n";
    }

    private function generateUUID(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
