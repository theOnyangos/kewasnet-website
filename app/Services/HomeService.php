<?php

namespace App\Services;

class HomeService
{
    protected $resourceModel;
    protected $forumMemberModel;

    public function __construct()
    {
        $this->resourceModel = model('App\Models\Resource');
        $this->forumMemberModel = model('App\Models\ForumMember');
    }

    public function getKnowledgeHubStats()
    {
        $totalResources     = $this->resourceModel->countAllResults();
        $totalMembers       = $this->forumMemberModel->countAllResults();

        return [
            'total_resources'    => $totalResources,
            'total_members'      => $totalMembers,
            'total_learning_hub' => 0
        ];
    }
}
