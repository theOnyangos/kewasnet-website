<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\Connection;
use App\Models\UserModel;
use App\Models\ChatTopicModal;
use Carbon\Carbon;

class ChatService
{
    protected $chatModel;
    protected $connectionModel;
    protected $userModel;
    protected $chatTopicModel;

    public function __construct()
    {
        $this->chatModel = new ChatMessage();
        $this->connectionModel = new Connection();
        $this->userModel = new UserModel();
        $this->chatTopicModel = new ChatTopicModal();
    }

    // render chat topics html
    public function renderChatTopicsHtml($topics)
    {
        $html = '';

        if (empty($topics)) {
            return '<div class="h-[200px] w-full flex justify-center items-center gap-3 bg-white/50 rounded-md shadow-sm">
                        <ion-icon name="warning-outline" class="text-[25px] text-gray-500"></ion-icon>
                        <p class="text-center text-gray-500 roboto">No Topics!</p>
                    </div>';
        }

        $totalTopics = count($topics);

        foreach ($topics as $key => $topic) {

            $totalConnections = $this->chatModel->where('topic_id', $topic['id'])->groupBy('sender_id')->orderBy('id', 'DESC')->countAllResults();

            $lastMessage = $this->chatModel->where('topic_id', $topic['id'])->orderBy('id', 'DESC')->first();

            $isLastItem = $key + 1 == $totalTopics ? true : false;

            $html .= view('frontend/account/networking_corner/topics', [
                'topic' => $topic,
                'totalConnections' => $totalConnections,
                'lastMessage' => $lastMessage,
                'isLastItem' => $isLastItem
            ]);
        }

        return $html;
    }

    // Render ChatsHtml
    public function renderChatsHtml($chats, $topicId = null)
    {
        $topicData = $this->chatTopicModel->getTopicData($topicId);

        $topicDate = Carbon::parse($topicData['created_at'])->format('d M, Y h:i A');

        if (empty($chats)) {
            return '<!-- Chat Creator Details -->
                    <div class="flex justify-center items-center gap-3 mb-5 border-b border-borderColor pb-5">
                        <ion-icon name="chatbox-ellipses-outline" class="text-[20px] text-gray-500"></ion-icon>
                        <p class="text-center text-gray-500 roboto text-sm">This conversation was started by <span class="font-bold roboto">'.$topicData['author_name'].'</span> on <span class="font-bold roboto">'.$topicDate.'</span></p>
                    </div>
                    <div class="h-[200px] w-full flex justify-center items-center gap-3 bg-blue-100 rounded-md mb-5 shadow-md">
                        <ion-icon name="warning-outline" class="text-[25px] text-gray-600"></ion-icon>
                        <p class="text-center text-gray-600 roboto">No chats found for this topic!</p>
                    </div>';
        }

        $html = '<!-- Chat Creator Details -->
                <div class="flex justify-center items-center gap-3 mb-5 border-b border-borderColor pb-5">
                    <ion-icon name="chatbox-ellipses-outline" class="text-[20px] text-gray-500"></ion-icon>
                    <p class="text-center text-gray-500 roboto text-sm">This conversation was started by <span class="font-bold roboto">'.$topicData['author_name'].'</span> on <span class="font-bold roboto">'.$topicDate.'</span></p>
                </div>

                <div class="flex items-start mb-4">
                    <div class="flex-shrink-0">
                        <img src="'.($topicData['author_picture'] ?? base_url("profile-avatar.png")).'" alt="'.$topicData['author_name'].'" class="w-8 h-8 rounded-full mr-2">
                    </div>
                    <div class="bg-gradient-to-r from-orange-500/50 to-orange-300/50 text-gray-800 py-2 px-4 rounded-md shadow-md">
                        <div class="flex justify-between items-center">
                            <p class="font-bold">'.$topicData['author_name'].' <span class="font-normal roboto">(Pinned)</span></p>
                        </div>
                        <p>'.$topicData['body'].'</p>
                    </div>
                </div>';

        foreach ($chats as $chat) {

            $senderDetails = $this->userModel->where('id', $chat['sender_id'])->first();

            $html .= view('frontend/account/networking_corner/chats', [
                'chat' => $chat,
                'user' => $senderDetails
            ]);
        }

        return $html;
    }

    // Render chat files
    public function renderChatFilesHtml($files)
    {
        $html = '';

        if (empty($files)) {
            return '<div class="h-[200px] w-full flex justify-center items-center gap-3 bg-blue-100 rounded-md shadow-md">
                        <ion-icon name="warning-outline" class="text-[25px] text-gray-600"></ion-icon>
                        <p class="text-sm md:text-md text-center text-gray-600 roboto">No files found for this chat!</p>
                    </div>';
        }

        $totalFiles = count($files);

        foreach ($files as $key => $file) {

            $senderDetails = $this->userModel->where('id', $file['user_id'])->first();

            $isLastItem = $key + 1 == $totalFiles ? true : false;

            $html .= view('frontend/account/networking_corner/chat_files', [
                'file' => $file,
                'user' => $senderDetails,
                'isLastItem' => $isLastItem
            ]);
        }

        return $html;
    }
}