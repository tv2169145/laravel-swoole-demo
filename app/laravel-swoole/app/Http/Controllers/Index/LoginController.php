<?php

namespace App\Http\Controllers\Index;

use App\Http\Controllers\Controller;
use SwooleTW\Http\Websocket\Websocket;

class LoginController extends Controller
{
    public function index(Websocket $websocket, $data)
    {
        /**
         * 这里就可以做业务处理，比如绑定用户和fd等
         */
        $websocket->emit('return', "我收到了你的消息" . json_encode($data));
    }

    public function getUsername(Websocket $websocket, $data)
    {
        dump("getUsername", $data);
        $r = [
            "connected_users" => [
                $data
            ]
        ];
        $websocket->emit('list_users', json_encode($r));
    }

    public function broadcast(Websocket $websocket, $data)
    {
        dump("broadcast", $data);
        $message = "{$data['username']}: {$data['message']}";
        $websocket->broadcast()->emit("broadcast", $message);
    }
}
