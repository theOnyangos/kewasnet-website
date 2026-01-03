<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ForumAndDiscussionSeeder extends Seeder
{
    public function run()
    {
        echo "🚀 Starting Forum and Discussion Seeding Process...\n\n";
        
        // First, seed forums (will skip if they already exist due to unique constraints)
        echo "📂 Step 1: Seeding Forums...\n";
        try {
            $this->call('ForumSeeder');
            echo "✅ Forums seeding completed.\n\n";
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "ℹ️ Forums already exist, skipping forum creation.\n\n";
            } else {
                echo "❌ Error seeding forums: " . $e->getMessage() . "\n\n";
                return;
            }
        }
        
        // Then, seed discussions
        echo "💬 Step 2: Seeding Discussions...\n";
        try {
            // Check if discussions already exist
            $existingDiscussions = $this->db->table('discussions')->countAllResults();
            
            if ($existingDiscussions > 0) {
                echo "ℹ️ Found {$existingDiscussions} existing discussions.\n";
                echo "⚠️ Skipping discussion seeding to avoid duplicates.\n";
                echo "💡 To reseed discussions, please truncate the discussions table first.\n\n";
            } else {
                $this->call('DiscussionSeeder');
                echo "✅ Discussions seeding completed.\n\n";
            }
        } catch (\Exception $e) {
            echo "❌ Error seeding discussions: " . $e->getMessage() . "\n\n";
            return;
        }
        
        // Display final summary
        echo "🎉 Forum and Discussion Seeding Process Completed!\n";
        echo "================================================\n";
        
        // Get final counts
        $forumCount = $this->db->table('forums')->where('is_active', 1)->countAllResults();
        $discussionCount = $this->db->table('discussions')->where('status', 'active')->countAllResults();
        
        echo "📊 Final Statistics:\n";
        echo "   • Active Forums: {$forumCount}\n";
        echo "   • Active Discussions: {$discussionCount}\n";
        echo "   • Average Discussions per Forum: " . round($discussionCount / max($forumCount, 1), 1) . "\n";
        echo "\n🚀 Your forum is now ready for community engagement!\n";
        
        // Display next steps
        echo "\n📋 Next Steps:\n";
        echo "   1. Visit your forum administration panel\n";
        echo "   2. Review and customize forum settings\n";
        echo "   3. Add forum moderators if needed\n";
        echo "   4. Test discussion creation and replies\n";
        echo "   5. Invite community members to join\n";
    }
}
