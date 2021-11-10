<?php

namespace App\Swoole;

class Parser extends \SwooleTW\Http\Websocket\Parser
{
    /**
     * Encode output payload for websocket push.
     * @param string $event
     * @param mixed $data
     * @return mixed
     */
    public function encode(string $event, $data)
    {
        $string = ['event' => $event, 'data' => $data];
        return json_encode($string);
    }

    /**
     * Input message on websocket connected.
     * Define and return event name and payload data here.
     * @param \Swoole\Websocket\Frame $frame
     * @return array
     */
    public function decode($frame)
    {
        //这里是解析客户端发来的数据，我们约定所有的传输都是json
        $json = $frame->data;
        $data = json_decode($json, true);
        if (!$data || !isset($data['event'])) {
            return ['event' => 'error', 'data' => $frame->data];
        }
        return ['event' => $data['event'], 'data' => $data['data'] ?? ''];
    }
}
