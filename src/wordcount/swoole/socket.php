<?php

$serv = new Swoole\Server('0.0.0.0', 9000, SWOOLE_BASE, SWOOLE_SOCK_TCP);
$serv->set(array(
    'worker_num' => 4,
    'daemonize' => false,
    'backlog' => 128,
));
$serv->on('connect', function($server, $fd, $reactorId){
	$redis = new \Redis();
	$redis->connect('127.0.0.1', 6379);
	while(true) {
		$data = $redis->rpop('tech_trend:collector_pipeline');
		if ($data) {
			$server->send($fd, $data . "\n");
		}
		usleep(10000);
	}
});
$serv->on('receive', function($server, $fd, $reactor_id, $data){
	//
});
$serv->start();
