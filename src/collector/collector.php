<?php

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

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
