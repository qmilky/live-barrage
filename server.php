<?php
$server = new swoole_websocket_server('127.0.0.1',9999);
$server->set(array(
    'reactor_num' => 100, //reactor thread num
    'worker_num' => 2, //worker process num
    'backlog' => 128, //listen backlog
    'max_request' => 10000,
    'dispatch_mod' => 1,
));
$server->on('open',function(swoole_websocket_server $server,$request){
    echo 'request room ID :'.$request->fd.PHP_EOL;
    if($request->server['path_info'] !== '/push')
    {
        if($request->server['path_info'] !== '/play')
        {
            $server->push($request->fd, json_encode(['status'=>404, 'message'=>'Live Stream Not Found']));
            $server->close($request->fd);
        }else{
            echo "server:handShake success with {$request->fd}".PHP_EOL;
        }
    }
});
$server->on('message',function(swoole_websocket_server $server,$frame){
    echo "receive from {$frame->fd}".PHP_EOL;
    $data = $frame->data;
    foreach($server->connections as $conn)
    {
        $server->push($conn,$data);

    }
});

$server->on('close',function($ser,$fd){
    echo 'client close:'.$fd.PHP_EOL;
});
$server->start();
