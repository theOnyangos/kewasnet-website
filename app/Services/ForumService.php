<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Forum;
use App\Models\UserModel;
use App\Models\ForumModerator;
use CodeIgniter\Validation\Validation;
use App\Exceptions\ValidationException;

class ForumService
{
    protected $userModel;
    protected $forumModel;
    protected $validation;
    protected $forumModeratorModel;

    public function __construct()
    {
        $this->forumModel = new Forum();
        $this->validation = \Config\Services::validation();
        $this->userModel = new UserModel();
        $this->forumModeratorModel = new ForumModerator();
    }

    public function createForum(array $forumData)
    {
        // Set validation rules
        $rules = [
            'name' => 'required|max_length[255]',
            'slug' => 'required|max_length[255]|is_unique[forums.slug]',
            'description' => 'required',
            'color' => 'permit_empty',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        $messages = [
            'name' => [
                'required' => 'Forum name is required',
                'max_length' => 'Forum name cannot exceed 255 characters'
            ],
            'slug' => [
                'required' => 'Slug is required',
                'max_length' => 'Slug cannot exceed 255 characters',
                'is_unique' => 'This slug is already in use'
            ],
            'description' => [
                'required' => 'Forum description is required'
            ],
        ];

        $this->validation->setRules($rules, $messages);
        
        if (!$this->validation->run($forumData)) {
            throw new ValidationException($this->validation->getErrors());
        }

        try {
            $insertData = [
                'name'        => $forumData['name'],
                'description' => $forumData['description'],
                'slug'        => $forumData['slug'],
                'icon'        => $forumData['icon'] ?? null,
                'color'       => $forumData['color'] ?? null,
                'sort_order'  => $forumData['sort_order'] ?? 0,
                'is_active'   => $forumData['is_active'] ?? 1,
                'is_draft'    => $forumData['is_draft'] ?? 0,
            ];

            // Let the model handle UUID, timestamps, and created_by
            return $this->forumModel->insert($insertData);
            
        } catch (\Exception $e) {
            log_message('error', 'Forum creation failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to create forum due to database error: ' . $e->getMessage());
        }
    }

    public function updateForum($forumId, array $forumData)
    {
        $rules = [
            'name' => 'required|max_length[255]',
            'description' => 'required',
            'color' => 'permit_empty',
            'is_active' => 'permit_empty|in_list[0,1]',
        ];

        $messages = [
            'name' => [
                'required' => 'Forum name is required',
                'max_length' => 'Forum name cannot exceed 255 characters'
            ],
            'description' => [
                'required' => 'Forum description is required'
            ],
        ];

        $this->validation->setRules($rules, $messages);
        
        if (!$this->validation->run($forumData)) {
            throw new ValidationException($this->validation->getErrors());
        }

        try {
            $insertData = [
                'name'        => $forumData['name'],
                'description' => $forumData['description'],
                'slug'        => $forumData['slug'],
                'icon'        => $forumData['icon'] ?? null,
                'color'       => $forumData['color'] ?? null,
                'sort_order'  => $forumData['sort_order'] ?? 0,
                'is_active'   => $forumData['is_active'] ?? 1,
                'is_draft'    => $forumData['is_draft'] ?? 0,
            ];

            return $this->forumModel->update($forumId, $insertData);
        } catch (\Exception $e) {
            log_message('error', 'Forum update failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to update forum');
        }
    }

    public function getSystemUsers($searchTerm = null, $limit = 10)
    {
        try {
            $users = $this->forumModel->getSystemUsers($searchTerm, $limit);
    
            $formattedUsers = array_map(function($user) {
                return [
                    'id'    => $user['id'],
                    'name'  => $user['first_name'] . ' ' . $user['last_name'],
                    'email' => $user['email'],
                    'role'  => $user['role'] ?? 'User',
                    'image' => null
                ];
            }, $users);
            
            return $formattedUsers;

        } catch (\Exception $e) {
            log_message('error', 'Failed to retrieve system users: ' . $e->getMessage());
            return [];
        }
    }

    public function addModerators($formData)
    {
        // Check if we're handling multiple moderators
        if (isset($formData['moderators']) && is_array($formData['moderators'])) {
            return $this->addMultipleModerators($formData['moderators']);
        }

        // Handle single moderator (backward compatibility)
        $rules = [
            'user_id' => 'required|is_not_unique[system_users.id]',
            'forum_id' => 'required|is_not_unique[forums.id]',
            'moderator_title' => 'permit_empty|max_length[100]',
        ];

        $messages = [
            'user_id' => [
                'required' => 'User ID is required',
                'is_not_unique' => 'User does not exist'
            ],
            'forum_id' => [
                'required' => 'Forum ID is required',
                'is_not_unique' => 'Forum does not exist'
            ],
            'moderator_title' => [
                'permit_empty' => 'Moderator title is optional',
                'max_length' => 'Moderator title cannot exceed 100 characters'
            ],
        ];

        $this->validation->setRules($rules, $messages);

        if (!$this->validation->run($formData)) {
            throw new ValidationException($this->validation->getErrors());
        }

        try {
            $insertData = [
                'user_id'         => $formData['user_id'],
                'forum_id'        => $formData['forum_id'],
                'moderator_title' => $formData['moderator_title'] ?? null,
            ];

            return $this->forumModeratorModel->assignModerator($insertData['forum_id'], $insertData['user_id'], $insertData['moderator_title']);
        } catch (\Exception $e) {
            log_message('error', 'Failed to add moderator: ' . $e->getMessage());
            throw new \RuntimeException('Failed to add moderator');
        }
    }

    /**
     * Add multiple moderators at once
     */
    private function addMultipleModerators($moderators)
    {
        if (empty($moderators)) {
            throw new ValidationException(['moderators' => 'At least one moderator is required']);
        }

        $errors = [];
        $successCount = 0;
        $skippedCount = 0;

        foreach ($moderators as $index => $moderator) {
            // Validate each moderator
            $rules = [
                'user_id' => 'required|is_not_unique[system_users.id]',
                'forum_id' => 'required|is_not_unique[forums.id]',
                'moderator_title' => 'permit_empty|max_length[100]',
            ];

            $this->validation->reset();
            $this->validation->setRules($rules);

            if (!$this->validation->run($moderator)) {
                $errors["moderator_{$index}"] = $this->validation->getErrors();
                continue;
            }

            try {
                // Check if user is already a moderator
                if ($this->forumModeratorModel->isModerator($moderator['forum_id'], $moderator['user_id'])) {
                    $skippedCount++;
                    continue;
                }

                // Add the moderator
                $this->forumModeratorModel->assignModerator(
                    $moderator['forum_id'],
                    $moderator['user_id'],
                    $moderator['moderator_title'] ?? 'Moderator'
                );
                $successCount++;
            } catch (\Exception $e) {
                log_message('error', 'Failed to add moderator at index ' . $index . ': ' . $e->getMessage());
                $errors["moderator_{$index}"] = $e->getMessage();
            }
        }

        // If no moderators were added, throw an error
        if ($successCount === 0 && !empty($errors)) {
            throw new ValidationException($errors);
        }

        return [
            'success_count' => $successCount,
            'skipped_count' => $skippedCount,
            'errors' => $errors
        ];
    }

    public function getModeratorsByForumId(string $forumId)
    {
        try {
            return $this->forumModeratorModel->getModeratorsByForumId($forumId);
        } catch (\Exception $e) {
            log_message('error', 'Failed to retrieve moderators: ' . $e->getMessage());
            return [];
        }
    }
}