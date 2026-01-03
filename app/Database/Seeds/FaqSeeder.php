<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class FaqSeeder extends Seeder
{
    public function run()
    {
        $faqs = [
            [
                'id' => Uuid::uuid4()->toString(),
                'question' => 'What is KEWASNET?',
                'answer' => 'The Kenya Water and Sanitation Civil Societies Network (KEWASNET) is a national membership organisation formed in 2007. It consists of Civil Society Organizations working in the Water, Sanitation, and Hygiene (WASH) sector.',
                'category' => 'general'
            ],
            [
                'id' => '062b15a3-aea3-4028-bc71-3e339cce84c7',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'When was KEWASNET established?',
                'answer' => 'KEWASNET was established in 2007.',
                'category' => 'general'
            ],
            [
                'id' => '0752b9f3-9f23-4868-be94-1a12066b9e3c',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'Under which legal framework is KEWASNET registered?',
                'answer' => 'KEWASNET is registered as a civil society organization (CSO) under the Societies Act Cap 108, Laws of Kenya.',
                'category' => 'general'
            ],
            [
                'id' => '9af7d4cb-0732-4def-a55f-f857dbc0d3b2',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'Who can become a member of KEWASNET?',
                'answer' => 'Membership to KEWASNET is open to Civil Society Organizations working in the Water, Sanitation, and Hygiene (WASH) sector.',
                'category' => 'membership'
            ],
            [
                'id' => 'e1a57742-46d5-49a6-a55a-51a0a381cf1f',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'How can an organization join KEWASNET?',
                'answer' => 'All interested organisations can apply for membership by submitting an application form and the required documentation specified by KEWASNET.',
                'category' => 'membership'
            ],
            [
                'id' => '6e71b397-6380-4ff3-9825-1eb2a4a26276',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'What are the benefits of being a member of KEWASNET?',
                'answer' => 'Members benefit from networking opportunities, capacity-building initiatives, access to information and resources, advocacy support, and a platform to influence WASH policies and practices in Kenya.',
                'category' => 'membership'
            ],
            [
                'id' => '14646efc-a6dd-4c3a-9cb9-47cff7055d71',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'What kind of activities does KEWASNET engage in?',
                'answer' => 'KEWASNET engages in advocacy, capacity building, networking, research, and information dissemination to promote effective Water, Sanitation, and Hygiene (WASH) services in Kenya.',
                'category' => 'general'
            ],
            [
                'id' => 'ce0dc3f3-4328-4fa7-8bd1-588f92ea816d',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'Does KEWASNET work with other organizations?',
                'answer' => 'Yes, KEWASNET collaborates with various stakeholders, including government agencies, development partners, private sector entities, and other civil society organisations to enhance WASH services in Kenya.',
                'category' => 'general'
            ],
            [
                'id' => 'd3f59f92-6d57-4383-aa19-85fe0ccc7f1b',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'How does KEWASNET influence WASH policies?',
                'answer' => 'KEWASNET participates in policy dialogues, conducts research, and engages in advocacy campaigns to influence and shape WASH policies at the national and county levels.',
                'category' => 'general'
            ],
            [
                'id' => '3cb623d3-ffd1-464e-a6a2-c40f91d93e59',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'How can I contact KEWASNET?',
                'answer' => 'You can contact KEWASNET through our official website, email, or phone. Specific contact details can be found on the KEWASNET website.',
                'category' => 'contact'
            ],
            [
                'id' => 'ddc621d9-4a09-4243-a8d4-b13b619c0047',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'How can I support KEWASNET initiatives?',
                'answer' => 'You can support KEWASNET by becoming a member, partnering on projects, volunteering, or providing financial or in-kind contributions to support their programs and activities.',
                'category' => 'general'
            ],
            [
                'id' => '93f6d133-2891-4118-8e36-ecda532c79ed',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'Where can I find more information about KEWASNET work?',
                'answer' => 'More information about KEWASNET work, projects, and initiatives can be found on our official website through the Knowledge Sharing Platform (KSP).',
                'category' => 'ksp'
            ],
            [
                'id' => '17ae6acf-3729-4279-9d54-553b78643484',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'What is Knowledge Sharing Platform (KSP)?',
                'answer' => 'The KSP is an online platform designed by KEWASNET to facilitate the sharing of knowledge, resources, and best practices among civil society organisations and stakeholders in the Water, Sanitation, and Hygiene (WASH) sector in Kenya.',
                'category' => 'ksp'
            ],
            [
                'id' => 'd6b62def-b0b8-422e-be6e-99f40b4bc71a',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'Who can access KSP?',
                'answer' => 'The KSP is accessible to all KEWASNET members, partners, and stakeholders involved in the WASH sector. It aims to provide valuable resources for anyone interested in improving water and sanitation services in Kenya. To access the KPS, click on the Knowledge Hub button on the top right of the website.',
                'category' => 'ksp'
            ],
            [
                'id' => '7122450d-fa28-40d4-8baa-e3317ed5cd84',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'What resources are available on the KSP?',
                'answer' => 'The KSP offers a variety of resources including research reports, policy briefs, case studies, training materials, toolkits, and discussion forums. These resources are designed to support the work of WASH stakeholders and enhance knowledge sharing.',
                'category' => 'ksp'
            ],
            [
                'id' => '90de537c-3aa6-49b0-b2dd-a13c21439a9a',
                'id' => Uuid::uuid4()->toString(),
                'question' => 'Can I participate in discussions and forums on the KSP?',
                'answer' => 'Yes, you can. You are encouraged to participate in discussions and forums on the KSP. These interactive features are designed to foster collaboration and exchange of ideas among WASH stakeholders.',
                'category' => 'ksp'
            ]
        ];

        // Using the model to insert data
        $model = new \App\Models\Faq();
        
        foreach ($faqs as $faq) {
            $model->insert($faq);
        }
    }
}