<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\Pillar;

class PillarSeeder extends Seeder
{
    public function run()
    {
        $model = new Pillar();

        $pillars = [
            [
                'slug' => 'wash',
                'title' => 'Water, Sanitation & Hygiene (WASH)',
                'icon' => 'droplets',
                'description' => 'Our WASH pillar focuses on improving access to safe water, adequate sanitation, and proper hygiene practices across Kenya.',
                'content' => '<p>Our WASH pillar focuses on improving access to safe water, adequate sanitation, and proper hygiene practices across Kenya. We work with communities, governments, and partners to implement sustainable solutions that address the country\'s most pressing WASH challenges.</p>
                <p>Key areas of focus include:</p>
                <ul>
                    <li>Community-led total sanitation</li>
                    <li>School WASH programs</li>
                    <li>Urban sanitation solutions</li>
                    <li>Water quality monitoring</li>
                    <li>Behavior change communication</li>
                </ul>',
                'image_path' => 'assets/new/tap_water.jpg',
                'button_text' => 'Explore WASH Resources',
                'button_link' => '/ksp/pillar-articles/72463798',
                'meta_title' => 'WASH Pillar - Water, Sanitation & Hygiene',
                'meta_description' => 'Learn about our WASH initiatives and resources for improving water, sanitation and hygiene in Kenya.',
                'is_active' => 1,
            ],
            [
                'slug' => 'governance',
                'title' => 'Governance',
                'icon' => 'scale',
                'description' => 'Our Governance pillar focuses on strengthening water sector policies, institutions, and regulatory frameworks.',
                'content' => '<p>Our Governance pillar focuses on strengthening water sector policies, institutions, and regulatory frameworks to ensure equitable and sustainable water resource management.</p>
                <p>Key areas of focus include:</p>
                <ul>
                    <li>Policy development and implementation</li>
                    <li>Institutional capacity building</li>
                    <li>Regulatory frameworks</li>
                    <li>Accountability mechanisms</li>
                    <li>Stakeholder engagement</li>
                </ul>',
                'image_path' => 'assets/new/governance.jpg',
                'button_text' => 'Explore Governance Resources',
                'button_link' => '/ksp/pillar-articles/72463799',
                'meta_title' => 'Governance Pillar - Water Sector Governance',
                'meta_description' => 'Learn about our governance initiatives for strengthening water sector policies and institutions.',
                'is_active' => 1,
            ],
            [
                'slug' => 'climate',
                'title' => 'Climate Change',
                'icon' => 'cloud-sun',
                'description' => 'Our Climate Change pillar addresses the impacts of climate variability on water resources and WASH services.',
                'content' => '<p>Our Climate Change pillar addresses the impacts of climate variability on water resources and WASH services, promoting resilience and adaptation strategies.</p>
                <p>Key areas of focus include:</p>
                <ul>
                    <li>Climate-resilient WASH infrastructure</li>
                    <li>Early warning systems</li>
                    <li>Drought and flood management</li>
                    <li>Ecosystem-based adaptation</li>
                    <li>Climate-smart water technologies</li>
                </ul>',
                'image_path' => 'assets/new/climate.jpg',
                'button_text' => 'Explore Climate Resources',
                'button_link' => '/ksp/pillar-articles/72463800',
                'meta_title' => 'Climate Change Pillar - Water and Climate Resilience',
                'meta_description' => 'Learn about our climate change initiatives for resilient water resource management.',
                'is_active' => 1,
            ],
            [
                'slug' => 'nexus',
                'title' => 'NEXUS',
                'icon' => 'link-2',
                'description' => 'Our NEXUS pillar promotes integrated approaches to water, energy, food, and ecosystem security.',
                'content' => '<p>Our NEXUS pillar promotes integrated approaches to water, energy, food, and ecosystem security, recognizing their interdependencies.</p>
                <p>Key areas of focus include:</p>
                <ul>
                    <li>Water-energy-food nexus planning</li>
                    <li>Cross-sectoral coordination</li>
                    <li>Resource use efficiency</li>
                    <li>Sustainable intensification</li>
                    <li>Ecosystem services valuation</li>
                </ul>',
                'image_path' => 'assets/new/nexus.jpg',
                'button_text' => 'Explore NEXUS Resources',
                'button_link' => '/ksp/pillar-articles/72463801',
                'meta_title' => 'NEXUS Pillar - Water, Energy, Food Security',
                'meta_description' => 'Learn about our integrated approaches to water, energy and food security.',
                'is_active' => 1,
            ],
            [
                'slug' => 'iwrm',
                'title' => 'IWRM',
                'icon' => 'map',
                'description' => 'Our IWRM pillar advances Integrated Water Resources Management principles for sustainable water use.',
                'content' => '<p>Our IWRM pillar advances Integrated Water Resources Management principles for sustainable water use across sectors and jurisdictions.</p>
                <p>Key areas of focus include:</p>
                <ul>
                    <li>Basin-scale planning</li>
                    <li>Stakeholder participation</li>
                    <li>Water allocation mechanisms</li>
                    <li>Conflict resolution</li>
                    <li>Monitoring and evaluation</li>
                </ul>',
                'image_path' => 'assets/new/iwrm.jpg',
                'button_text' => 'Explore IWRM Resources',
                'button_link' => '/ksp/pillar-articles/72463802',
                'meta_title' => 'IWRM Pillar - Integrated Water Resources Management',
                'meta_description' => 'Learn about our IWRM initiatives for sustainable water resource management.',
                'is_active' => 1,
            ]
        ];

        foreach ($pillars as $pillar) {
            $model->insert($pillar);
        }
    }
}