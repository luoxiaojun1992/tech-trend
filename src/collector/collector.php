<?php

require_once __DIR__ . '/../common/Helper.php';

$options = \getOptions($argv, ['h', 'p']);
$redisHost = isset($options['h']) ? $options['h'] : '127.0.0.1';
$redisPort = intval(isset($options['p']) ? $options['p'] : 6379);

$redis = new \Redis();
$redis->connect($redisHost, $redisPort);

while(true) {
	$data = $redis->rpop('flink_word_count_analyse');
	if ($data) {
		list($word, $count) = explode(' : ', $data);
		$redis->zincrby('tech_trend:result', doubleval($count), $word);
		if ($redis->ttl('tech_trend:result') < 0) {
			$redis->expire('tech_trend:result', 3600);
		}
	}
}
