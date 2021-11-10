<?php

namespace App\Swoole;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Swoole\WebSocket\Frame;
use SwooleTW\Http\Websocket\HandlerContract;
use SwooleTW\Http\Server\Facades\Server as ClientServer;
use SwooleTW\Http\Server\Manager;

class WebsocketHandler implements HandlerContract
{
    private $server;

    public function __construct()
    {
        /** @var Manager $manager */
        $this->server = App::make(ClientServer::class);
    }

    /**
     * "onOpen" listener.
     * @param int $fd
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    public function onOpen($fd, Request $request)
    {
        /**
         * 客户端建立起长链接后，返回客户端fd
         */
        $this->server->push($fd, json_encode(['event' => 'open', 'data' => ['fd' => $fd]]));
        return true;
    }

    /**
     * "onMessage" listener.
     *  only triggered when event handler not found
     * @param \Swoole\Websocket\Frame $frame
     */
    public function onMessage(Frame $frame)
    {
        return true;
    }

    /**
     * "onClose" listener.
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose($fd, $reactorId)
    {
        return;
    }
}
