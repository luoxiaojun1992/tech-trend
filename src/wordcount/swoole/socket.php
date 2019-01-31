<?php

require_once __DIR__ . '/../common/Helper.php';

$options = \getOptions($argv, ['sh', 'sp', 'rh', 'rp']);
$swHost = isset($options['sh']) ? $options['sh'] : '0.0.0.0';
$swPort = intval(isset($options['sp']) ? $options['sp'] : 9000);
$redisHost = isset($options['rh']) ? $options['rh'] : '127.0.0.1';
$redisPort = intval(isset($options['rp']) ? $options['rp'] : 6379);

$serv = new Swoole\Server($swHost, $swPort, SWOOLE_BASE, SWOOLE_SOCK_TCP);
$serv->set(array(
    'worker_num' => 4,
    'daemonize' => true,
    'backlog' => 128,
));
$serv->on('connect', function($server, $fd, $reactorId) use ($redisHost, $redisPort) {
	$redis = new \Redis();
	$redis->connect($redisHost, $redisPort);
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
