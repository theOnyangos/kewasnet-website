<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class DiscussionSeeder extends Seeder
{
    public function run()
    {
        // Get all existing forums
        $forums = $this->db->table('forums')->where('is_active', 1)->get()->getResult();
        
        if (empty($forums)) {
            echo "❌ No active forums found. Please run ForumSeeder first.\n";
            return;
        }

        // Get some users for discussion creators
        $users = $this->db->table('users')->limit(5)->get()->getResult();
        
        if (empty($users)) {
            echo "❌ No users found. Please ensure users exist in the database.\n";
            return;
        }

        $discussions = [];
        $discussionCount = 0;

        // Water Quality Management Forum Discussions
        $waterQualityDiscussions = [
            [
                'title' => 'Best Practices for Water Quality Testing in Rural Areas',
                'content' => '<p>I\'ve been working on improving water quality testing protocols for rural water schemes. What are your experiences with portable testing kits?</p><p>Key considerations I\'ve found:</p><ul><li>Cost-effectiveness of different testing methods</li><li>Training requirements for local operators</li><li>Frequency of testing based on risk assessment</li><li>Record keeping and data management</li></ul><p>Would love to hear about successful implementation strategies and any challenges you\'ve faced.</p>',
                'tags' => '["water-testing", "rural-water", "quality-control", "protocols"]',
                'is_featured' => true,
            ],
            [
                'id' => 'bd208720-2302-485c-8a83-84746c40abc8',
                'title' => 'Chlorination Challenges in Gravity-Fed Systems',
                'content' => '<p>We\'re experiencing inconsistent chlorine residuals in our gravity-fed water system. The chlorine levels are adequate at the source but decrease significantly by the time water reaches the distribution points.</p><p>Current setup:</p><ul><li>Source: Protected spring</li><li>Distribution: 15km gravity-fed network</li><li>Treatment: Tablet chlorination at source</li><li>Serving: 2,500 people across 12 villages</li></ul><p>Has anyone successfully addressed similar issues? Considering multiple dosing points but concerned about operational complexity.</p>',
                'tags' => '["chlorination", "gravity-systems", "water-treatment", "distribution"]',
                'is_pinned' => true,
            ],
            [
                'id' => '4845178d-2cf0-4a89-b0c9-5ddc76cede70',
                'title' => 'Turbidity Reduction Techniques for Surface Water Sources',
                'content' => '<p>Looking for cost-effective solutions to reduce turbidity in surface water sources, especially during rainy seasons.</p><p>We\'ve tried:</p><ul><li>Sand filtration - works but maintenance intensive</li><li>Coagulation with alum - effective but requires skilled operation</li><li>Settling tanks - limited effectiveness during heavy rains</li></ul><p>What innovative approaches have you implemented? Particularly interested in community-managed solutions.</p>',
                'tags' => '["turbidity", "surface-water", "filtration", "community-management"]',
            ],
        ];

        // Borehole Construction & Maintenance Discussions
        $boreholeDiscussions = [
            [
                'title' => 'Solar Pump Installation Best Practices',
                'content' => '<p>Installing solar pumping systems for community boreholes. Looking for guidance on:</p><ul><li>Optimal panel sizing for different borehole yields</li><li>Controller programming for water level management</li><li>Protection against theft and vandalism</li><li>Maintenance schedules and spare parts inventory</li></ul><p>What has worked well in your experience? Any manufacturer recommendations?</p>',
                'tags' => '["solar-pumps", "borehole", "renewable-energy", "community-water"]',
                'is_featured' => true,
            ],
            [
                'id' => 'c27935d2-5165-43bf-a53e-5c385d521745',
                'title' => 'Borehole Yield Decline - Diagnosis and Solutions',
                'content' => '<p>Our community borehole has experienced a 40% yield decline over the past two years. Initial yield was 15m³/hour, now down to 9m³/hour.</p><p>Details:</p><ul><li>Depth: 80 meters</li><li>Age: 8 years</li><li>Pump type: Submersible centrifugal</li><li>Static water level: Has dropped 3 meters</li></ul><p>Considering rehabilitation options. What diagnostic steps would you recommend before deciding on treatment?</p>',
                'tags' => '["borehole-maintenance", "yield-decline", "rehabilitation", "diagnostics"]',
            ],
            [
                'id' => 'd75d8801-bfa1-4d4c-ae35-87c3d31d3138',
                'title' => 'Hand Pump vs. Motorized Systems: Cost-Benefit Analysis',
                'content' => '<p>Community is debating between upgrading to a motorized system or continuing with hand pumps for their three boreholes.</p><p>Current situation:</p><ul><li>3 hand pumps serving 800 people</li><li>Frequent breakdowns requiring external technicians</li><li>Limited daily yield affecting water access</li><li>High maintenance costs</li></ul><p>What factors should guide this decision? Long-term sustainability is our priority.</p>',
                'tags' => '["hand-pumps", "motorized-systems", "cost-analysis", "sustainability"]',
            ],
        ];

        // Community Water Projects Discussions
        $communityDiscussions = [
            [
                'title' => 'Successful Community Ownership Models',
                'content' => '<p>Sharing our 5-year journey of transitioning from NGO-managed to community-owned water system.</p><p>Key success factors:</p><ul><li>Gradual capacity building over 18 months</li><li>Transparent financial management training</li><li>Technical skills development for local technicians</li><li>Strong water committee governance</li></ul><p>Happy to share detailed implementation guide. What models have worked in your communities?</p>',
                'tags' => '["community-ownership", "capacity-building", "governance", "sustainability"]',
                'is_featured' => true,
            ],
            [
                'id' => 'c827b60d-0fbf-46d3-9809-dc74dc2223db',
                'title' => 'Gender Inclusive Water Committee Formation',
                'content' => '<p>Working on ensuring meaningful women\'s participation in water committee leadership. Current challenge: cultural barriers to women taking leadership roles.</p><p>Strategies we\'re trying:</p><ul><li>Separate women\'s meetings initially</li><li>Training on women\'s water management rights</li><li>Economic incentives through water-related enterprises</li><li>Partnership with local women\'s groups</li></ul><p>What approaches have been effective in similar contexts?</p>',
                'tags' => '["gender-inclusion", "water-committees", "leadership", "participation"]',
            ],
            [
                'id' => '8eda635d-42cd-4d75-a7a7-178181018e1a',
                'title' => 'Community Contribution Models for Project Sustainability',
                'content' => '<p>Designing contribution models that ensure long-term project sustainability while remaining affordable for low-income households.</p><p>Options under consideration:</p><ul><li>Fixed monthly fees</li><li>Pay-per-use systems</li><li>Labor contribution alternatives</li><li>Tiered pricing based on household income</li></ul><p>What has worked best for maintaining system operations and community buy-in?</p>',
                'tags' => '["community-contributions", "sustainability", "affordability", "financing"]',
            ],
        ];

        // Create a comprehensive list of discussion templates for all forums
        $forumDiscussionTemplates = [
            'Water Quality Management' => $waterQualityDiscussions,
            'Borehole Construction & Maintenance' => $boreholeDiscussions,
            'Community Water Projects' => $communityDiscussions,
            'Water Conservation & Climate' => [
                [
                    'title' => 'Rainwater Harvesting Systems for Drought Resilience',
                    'content' => '<p>Implementing large-scale rainwater harvesting to supplement water supply during dry seasons. Looking for design guidance and community management strategies.</p>',
                    'tags' => '["rainwater-harvesting", "drought-resilience", "climate-adaptation"]',
                ],
            [
                'id' => '19285324-b19a-4dd4-b5c0-8ed4cc464470',
                    'title' => 'Climate Change Impact on Groundwater Recharge',
                    'content' => '<p>Monitoring data shows declining groundwater levels. Discussing recharge enhancement techniques and climate adaptation strategies for water security.</p>',
                    'tags' => '["groundwater", "climate-change", "recharge", "monitoring"]',
                ],
            [
                'id' => '0092e1eb-51da-410c-ace5-34151088b51a',
                    'title' => 'Water Conservation Education Programs',
                    'content' => '<p>Developing effective community education programs on water conservation. Sharing successful awareness campaigns and behavior change strategies.</p>',
                    'tags' => '["conservation", "education", "awareness", "behavior-change"]',
                ],
            ],
            'Water Policy & Governance' => [
                [
                    'title' => 'Implementing New Water Sector Regulations',
                    'content' => '<p>Discussion on recent policy changes and their implementation challenges at community level. Sharing experiences and compliance strategies.</p>',
                    'tags' => '["policy", "regulations", "compliance", "implementation"]',
                ],
            [
                'id' => '5024630a-4b39-421e-84ce-a358238572ec',
                    'title' => 'Water Rights and Community Conflicts Resolution',
                    'content' => '<p>Addressing conflicts over water rights between communities. Discussing mediation approaches and legal frameworks for resolution.</p>',
                    'tags' => '["water-rights", "conflicts", "mediation", "legal-frameworks"]',
                ],
            [
                'id' => '815bb36e-7ba9-4b83-a27a-e7e8b22a25f0',
                    'title' => 'Participatory Planning in Water Projects',
                    'content' => '<p>Best practices for involving communities in water project planning and decision-making processes. Ensuring meaningful participation.</p>',
                    'tags' => '["participatory-planning", "community-involvement", "decision-making"]',
                ],
            ],
            'Water Safety & Health' => [
                [
                    'title' => 'Waterborne Disease Prevention Strategies',
                    'content' => '<p>Comprehensive approaches to preventing waterborne diseases in communities. Discussing water treatment, hygiene education, and health monitoring.</p>',
                    'tags' => '["disease-prevention", "hygiene", "health", "treatment"]',
                ],
            [
                'id' => 'f3ccd52a-e377-4e29-90e7-e8031a025ee2',
                    'title' => 'Water Safety Plans for Small Communities',
                    'content' => '<p>Developing and implementing Water Safety Plans for small water systems. Sharing templates and implementation experiences.</p>',
                    'tags' => '["water-safety-plans", "small-communities", "risk-assessment"]',
                ],
            [
                'id' => '16016240-e09d-450c-8b4c-fa85db60f745',
                    'title' => 'Emergency Water Response Protocols',
                    'content' => '<p>Establishing emergency response protocols for water system failures. Discussing contingency planning and emergency water supply options.</p>',
                    'tags' => '["emergency-response", "contingency-planning", "crisis-management"]',
                ],
            ],
            'Sustainable Water Technologies' => [
                [
                    'title' => 'IoT Monitoring Systems for Remote Water Points',
                    'content' => '<p>Implementing IoT solutions for monitoring remote water systems. Discussing sensor technologies, data management, and cost-effectiveness.</p>',
                    'tags' => '["IoT", "monitoring", "remote-systems", "technology"]',
                ],
            [
                'id' => 'dcf93f45-54e5-4c83-82e1-3d7a46b4d276',
                    'title' => 'Energy-Efficient Water Treatment Solutions',
                    'content' => '<p>Exploring low-energy water treatment technologies suitable for off-grid communities. Comparing different approaches and their sustainability.</p>',
                    'tags' => '["energy-efficiency", "treatment", "off-grid", "sustainability"]',
                ],
            [
                'id' => '03f7d489-94b9-4e06-a31b-10c163db1975',
                    'title' => 'Smart Water Meters for Community Systems',
                    'content' => '<p>Evaluating smart metering solutions for community water systems. Discussing benefits, challenges, and implementation strategies.</p>',
                    'tags' => '["smart-meters", "community-systems", "monitoring", "efficiency"]',
                ],
            ],
            'Training & Capacity Building' => [
                [
                    'title' => 'Technical Training Curriculum for Local Technicians',
                    'content' => '<p>Developing comprehensive training programs for local water technicians. Sharing curriculum ideas and certification approaches.</p>',
                    'tags' => '["technical-training", "curriculum", "certification", "capacity-building"]',
                ],
            [
                'id' => 'e8f09bd1-159b-4f15-b92a-22cb13b3bd30',
                    'title' => 'Financial Management Training for Water Committees',
                    'content' => '<p>Essential financial management skills for water committee members. Discussing training methods and ongoing support systems.</p>',
                    'tags' => '["financial-management", "water-committees", "training", "governance"]',
                ],
            [
                'id' => '05aff3d9-a641-43c8-b3bc-92d1a882ef84',
                    'title' => 'Knowledge Sharing Platforms and Networks',
                    'content' => '<p>Building effective knowledge sharing networks among water professionals. Discussing platform options and networking strategies.</p>',
                    'tags' => '["knowledge-sharing", "networks", "platforms", "collaboration"]',
                ],
            ],
            'Global Water Initiatives' => [
                [
                    'title' => 'SDG 6 Implementation Progress and Challenges',
                    'content' => '<p>Sharing progress on SDG 6 targets at national and local levels. Discussing implementation challenges and innovative solutions.</p>',
                    'tags' => '["SDG6", "implementation", "progress", "challenges"]',
                ],
            [
                'id' => '1485edf0-1a25-4520-a8c5-68ac9142ffd4',
                    'title' => 'Cross-Border Water Management Cooperation',
                    'content' => '<p>Discussing collaborative approaches to transboundary water management. Sharing successful cooperation models and frameworks.</p>',
                    'tags' => '["transboundary", "cooperation", "management", "collaboration"]',
                ],
            [
                'id' => '64a328da-a582-4ef8-835f-7573cb176159',
                    'title' => 'International Funding Opportunities for Water Projects',
                    'content' => '<p>Overview of current international funding opportunities for water sector projects. Sharing application experiences and success stories.</p>',
                    'tags' => '["funding", "international", "grants", "financing"]',
                ],
            ],
            'General Discussion' => [
                [
                    'title' => 'Welcome to KEWASNET Forum Community',
                    'content' => '<p>Welcome to the KEWASNET professional forum! Please introduce yourself and share your water sector background. Looking forward to productive discussions and knowledge sharing.</p>',
                    'tags' => '["welcome", "introductions", "community", "networking"]',
                    'is_pinned' => true,
                ],
            [
                'id' => 'fb608c29-ab5d-463e-89cf-e16c34059983',
                    'title' => 'Upcoming Water Sector Events and Conferences',
                    'content' => '<p>Share information about upcoming water sector events, conferences, and training opportunities. Help fellow professionals stay informed about networking and learning opportunities.</p>',
                    'tags' => '["events", "conferences", "training", "networking"]',
                ],
            [
                'id' => 'eacf3456-8868-4615-b48e-e49376163745',
                    'title' => 'Career Opportunities in Water Sector',
                    'content' => '<p>Job postings, internship opportunities, and career advice for water sector professionals. Share opportunities and seek career guidance from experienced professionals.</p>',
                    'tags' => '["careers", "jobs", "opportunities", "advice"]',
                ],
            ],
        ];

        // Create discussions for each forum
        foreach ($forums as $forum) {
            $templates = $forumDiscussionTemplates[$forum->name] ?? [];
            
            if (empty($templates)) {
                // Create generic discussions if no specific templates
                $templates = [
                    [
                        'title' => "Introduction to {$forum->name}",
                        'content' => "<p>Welcome to the {$forum->name} discussion area. Please share your experiences and questions related to this topic.</p>",
                        'tags' => '["introduction", "welcome"]',
                    ],
            [
                'id' => '8e1e1190-1ae7-44ae-88a3-e75c8f0ecdac',
                        'title' => "Best Practices in {$forum->name}",
                        'content' => "<p>Let's discuss best practices and lessons learned in {$forum->name}. Share your successful approaches and challenges.</p>",
                        'tags' => '["best-practices", "lessons-learned"]',
                    ],
            [
                'id' => 'ff0f146c-32cb-4160-99d0-6a2952595130',
                        'title' => "Current Challenges in {$forum->name}",
                        'content' => "<p>What are the main challenges facing {$forum->name} today? Let's discuss and explore potential solutions together.</p>",
                        'tags' => '["challenges", "solutions"]',
                    ],
                ];
            }

            foreach ($templates as $index => $template) {
                $randomUser = $users[array_rand($users)];
                
                $discussion = [
                    'id' => Uuid::uuid4()->toString(),
                    'forum_id' => $forum->id,
                    'user_id' => $randomUser->id,
                    'title' => $template['title'],
                    'slug' => url_title($template['title'], '-', true),
                    'content' => $template['content'],
                    'tags' => $template['tags'] ?? '[]',
                    'is_pinned' => $template['is_pinned'] ?? false,
                    'is_locked' => false,
                    'is_featured' => $template['is_featured'] ?? false,
                    'view_count' => rand(5, 150),
                    'reply_count' => 0,
                    'like_count' => rand(0, 25),
                    'last_reply_at' => null,
                    'last_reply_by' => null,
                    'status' => 'active',
                    'created_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
                    'updated_at' => date('Y-m-d H:i:s', strtotime('-' . rand(1, 30) . ' days')),
                    'deleted_at' => null,
                ];

                $discussions[] = $discussion;
                $discussionCount++;
            }
        }

        // Insert all discussions
        foreach ($discussions as $discussion) {
            $this->db->table('discussions')->insert($discussion);
        }

        // Update forum discussion counts
        foreach ($forums as $forum) {
            $forumDiscussionCount = count($forumDiscussionTemplates[$forum->name] ?? []);
            if ($forumDiscussionCount === 0) $forumDiscussionCount = 3; // Generic discussions
            
            $this->db->table('forums')
                     ->where('id', $forum->id)
                     ->update(['total_discussions' => $forumDiscussionCount]);
        }

        echo "✅ Successfully created {$discussionCount} discussions across " . count($forums) . " forums!\n";
        echo "📋 Discussions created:\n";
        
        foreach ($forums as $forum) {
            $forumDiscussionCount = count($forumDiscussionTemplates[$forum->name] ?? []);
            if ($forumDiscussionCount === 0) $forumDiscussionCount = 3;
            echo "   • {$forum->name}: {$forumDiscussionCount} discussions\n";
        }
        
        echo "\n🎯 All discussions are active and ready for community engagement!\n";
        echo "📊 Features added:\n";
        echo "   • Featured discussions for highlighting important topics\n";
        echo "   • Pinned discussions for announcements\n";
        echo "   • Realistic view counts and likes\n";
        echo "   • Relevant tags for better categorization\n";
        echo "   • Rich content with proper formatting\n";
    }
}
