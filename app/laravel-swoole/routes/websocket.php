<?php


use Illuminate\Http\Request;
use SwooleTW\Http\Websocket\Facades\Websocket;

/*
|--------------------------------------------------------------------------
| Websocket Routes
|--------------------------------------------------------------------------
|
| Here is where you can register websocket events for your application.
|
*/

Websocket::on('connect', function ($websocket, Request $request) {
    // called while socket on connect
    echo "connect";
//    $websocket->emit('connection success', "success");
});

Websocket::on('disconnect', function ($websocket) {
    // called while socket on disconnect
    echo "disconnect";
//    $websocket->emit('disconnect', [456]);
});

Websocket::on('example', function ($websocket, $data) {
    $websocket->broadcast()->emit('example', 'this is a test');
});

//// username
//Websocket::on('username', function ($websocket, $data) {
//    $websocket->emit('user_list', $data);
//});
////broadcast
//Websocket::on('broadcast', function ($websocket, $data) {
//    dump($data);
//    $websocket->broadcast()->emit('example', 'this is a test');
//});

Websocket::on('login','App\Http\Controllers\Index\LoginController@index');
Websocket::on('username','App\Http\Controllers\Index\LoginController@getUsername');
Websocket::on('broadcast','App\Http\Controllers\Index\LoginController@broadcast');
