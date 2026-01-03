<?php

namespace App\Models;

use CodeIgniter\Model;

class Notification extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'user_id',
        'type',
        'title',
        'message',
        'icon',
        'action_url',
        'action_text',
        'reference_id',
        'reference_type',
        'status',
        'read_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation rules
    protected $validationRules = [
        'user_id'  => 'required',
        'message'  => 'required|string',
        'type'     => 'in_list[success,warning,info,error,system]',
        'status'   => 'in_list[read,unread]',
    ];

    protected $validationMessages = [
        'user_id' => [
            'required' => 'User ID is required',
        ],
        'message' => [
            'required' => 'Message is required',
        ],
    ];

    /**
     * Get unread notifications for a user
     */
    public function getUnreadForUser(int|string $userId, int $limit = 10)
    {
        return $this->where('user_id', $userId)
            ->where('status', 'unread')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get all notifications for a user
     */
    public function getForUser(int|string $userId, int $limit = 50, int $offset = 0)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit, $offset)
            ->findAll();
    }

    /**
     * Get unread count for a user
     */
    public function getUnreadCount(int|string $userId): int
    {
        return $this->where('user_id', $userId)
            ->where('status', 'unread')
            ->countAllResults();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead(int $notificationId): bool
    {
        return $this->update($notificationId, [
            'status' => 'read',
            'read_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsReadForUser(int|string $userId): bool
    {
        return $this->where('user_id', $userId)
            ->where('status', 'unread')
            ->set([
                'status' => 'read',
                'read_at' => date('Y-m-d H:i:s'),
            ])
            ->update();
    }

    /**
     * Delete all read notifications for a user
     */
    public function deleteReadForUser(int|string $userId): bool
    {
        return $this->where('user_id', $userId)
            ->where('status', 'read')
            ->delete();
    }
}
