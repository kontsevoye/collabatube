<?php

declare(strict_types=1);

namespace App\Controller;

use Hyperf\Config\Annotation\Value;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

/**
 * @Controller
 */
class FrontendController extends AbstractController
{
    /**
     * @Value("socketio_server_url")
     */
    private string $socketIoServerUrl = '';

    /**
     * @RequestMapping(path="/ws-poc", methods="get")
     */
    public function POC(ResponseInterface $response): PsrResponseInterface
    {
        $data = <<<EOF
<!DOCTYPE html>
<html lang="en">
<script src="https://cdn.bootcss.com/socket.io/2.3.0/socket.io.js"></script>
<script>
    window.socket = io('{$this->socketIoServerUrl}', { transports: ["websocket"] });
    socket.on('connect', data => {
        socket.emit('event', 'hello, hyperf', (...args) => {console.log({type: 'emit-event', args});});
        socket.emit('join-room', 'room1', (...args) => {console.log({type: 'emit-join-room', args});});
        setInterval(function () {
            socket.emit('say', '{"room":"room1", "message":"interval message."}', (...args) => {console.log({type: 'emit-say', args});});
        }, 5000);
    });
    socket.on('event', (...args) => {console.log({type: 'on-event', args});});
</script>
</html>
EOF;
        return $response
            ->raw($data)
            ->withStatus(200)
            ->withHeader('content-type', 'text/html; charset=utf-8');
    }
}
