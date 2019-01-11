<?php

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

while(true) {
	$data = $redis->rpop('flink_word_count_analyse');
	if ($data) {
		var_dump($data);
	}
}

