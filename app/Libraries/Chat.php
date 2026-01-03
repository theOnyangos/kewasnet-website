<?php
namespace App\Libraries;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use App\Models\Connection;
use App\Models\UserModel;
use App\Models\ChatMessage;
use Carbon\Carbon;
use App\Models\ChatTopicModal;
use App\Libraries\ClientAuth;

class Chat implements MessageComponentInterface {

    protected $clients;
    protected $topicModel;
    protected $clientAuthentication;
    protected $userModel;
    protected $connectionsModel;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
        $this->topicModel = new ChatTopicModal();
        $this->clientAuthentication = new ClientAuth();
        $this->userModel = new UserModel();
        $this->connectionsModel = new Connection();
    }

    public function onOpen(ConnectionInterface $conn) {
        // Get access token from parameters
        $params = $conn->httpRequest->getUri()->getQuery();

        // Get the topic name from parameters
        $topicId = explode('=', $params)[1];

        // Get the topic id from the topic name
        $topic = $this->topicModel->where(array('id' => $topicId))->first();

        // Get the user id from the access token
        $userId = $this->clientAuthentication::getId();
        $user = $this->userModel->find($userId);

        // Check if the user exists
        if (!$user) {
            $conn->close();
            return;
        }

        // Add user and topic to connection object
        $conn->user = $user;
        $conn->topic = $topic;
        $this->clients->attach($conn);

        $this->userModel->where('id', $user['id'])->set(array('status' => 'online'))->update();
        $this->connectionsModel->where(array('user_id' => $user['id']))->delete();

        $this->connectionsModel->insert(array(
            'user_id' => $user['id'],
            'resource_id' => $conn->resourceId,
            'status' => 'online',
            'name' => $user['first_name'] . ' ' . $user['last_name'],
        ));

        // Send the user list to the connected user
        $users = $this->connectionsModel->findAll();
        $users = ['users' => $users];

        foreach ($this->clients as $client) {
            $client->send(json_encode($users));
        }

        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = count($this->clients) - 1;
        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

        foreach ($this->clients as $client) {
            if ($from !== $client && $from->topic->id == $client->topic->id) {
                $data = [
                    'from' => $from->user['username'],
                    'message' => $msg,
                    'created_at' => Carbon::now()->diffForHumans() // Human readable time
                ];

                $chatMessageModel = new ChatMessage();
                $chatMessageModel->insert(array(
                    'from' => $from->user['id'],
                    'to' => $client->user['id'],
                    'message' => $msg,
                    'created_at' => date('Y-m-d H:i:s')
                ));

                // The sender is not the receiver, send to each client connected
                $client->send($data);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        $connectionModel = new Connection();
        $connectionModel->where(array('resource_id' => $conn->resourceId))->delete();

        // Get all users in group chat
        $users = $connectionModel->where(['topic_id' => $conn->topic->id])->findAll();
        $users = ['users' => $users];

        foreach ($this->clients as $client) {
            $client->send(json_encode($users));
        }

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }
}