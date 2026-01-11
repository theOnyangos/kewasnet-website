<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSocialMediaToLeadershipTeam extends Migration
{
    public function up()
    {
        // Check if table exists and column doesn't exist
        if ($this->db->tableExists('leadership_team')) {
            if (!$this->db->fieldExists('social_media', 'leadership_team')) {
                $fields = [
                    'social_media' => [
                        'type' => 'TEXT',
                        'null' => true,
                        'comment' => 'JSON array of social media links with platform and URL'
                    ]
                ];
                
                $this->forge->addColumn('leadership_team', $fields);
            }
        }
    }

    public function down()
    {
        $this->forge->dropColumn('leadership_team', 'social_media');
    }
}
