<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\SocketIOServer\Annotation\Event;
use Hyperf\SocketIOServer\Annotation\SocketIONamespace;
use Hyperf\SocketIOServer\BaseNamespace;
use Hyperf\SocketIOServer\Socket;
use Hyperf\Utils\Codec\Json;

/**
 * @SocketIONamespace("/")
 */
class WebSocketController extends BaseNamespace
{
    /**
     * @Event("event")
     *
     * @return bool data returned to client. Can be any type eg string, number.
     */
    public function onEvent(Socket $socket, string $data): bool
    {
        var_dump(['method' => 'event', 'data' => $data]);

        return true;
    }

    /**
     * @Event("join-room")
     */
    public function onJoinRoom(Socket $socket, string $room): bool
    {
        var_dump(['method' => 'join-room', 'data' => $room]);

        $socket->join($room);
        $socket->to($room)->emit('event', "{$socket->getSid()} has joined {$room}");
        $clientsCount = count($socket->getAdapter()->clients($room));
        var_dump(['clients' => $socket->getAdapter()->clients($room)]);
        // broadcast to room except this client
        $socket->to($room)->emit('event', "There are {$clientsCount} clients in {$room}");
        // notify this client only
        $socket->emit('event', "There are {$clientsCount} clients in {$room}");
        // notify all except this client
        // $this->emit('event', "There are {$clientsCount} clients in {$room}");

        return true;
    }

    /**
     * @Event("say")
     */
    public function onSay(Socket $socket, string $data): bool
    {
        var_dump(['method' => 'say', 'data' => $data, 'sid' => $socket->getSid()]);

        $data = Json::decode($data);
        $this->emitToCurrentClient($socket, 'event', "{$socket->getSid()} say: {$data['message']}");
        $this->emitToRoomExceptCurrentClient(
            $socket,
            $data['room'],
            'event',
            "{$socket->getSid()} say: {$data['message']}"
        );

        return true;
    }

    private function emitToCurrentClient(Socket $socket, string $event = 'event', string $data = '')
    {
        $socket->emit($event, $data);
    }

    private function emitToRoomExceptCurrentClient(
        Socket $socket,
        string $room,
        string $event = 'event',
        string $data = ''
    ) {
        $socket->to($room)->emit($event, $data);
    }
}
