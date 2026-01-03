<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\Connection;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use App\Libraries\Chat;

class ChatServer extends BaseController
{
    public function index()
    {
        // Check if the server is running from cli
        if (!is_cli()) {
            return $this->response->setStatusCode(
                ResponseInterface::HTTP_INTERNAL_SERVER_ERROR)->setJSON(
                    ['message' => 'You can only run the chat server from the command line.']
                );
        }

        $port = 8000;

        // Start the WebSocket server
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Chat()
                )
            ),
            $port
        );

        // Clear the connections table
        $connectionModel = new Connection();
        $connectionModel->truncate();

        // Print message to indicate server is running
        fwrite(STDOUT, "WebSocket server is running on port $port\n");

        $server->run();
    }
}

