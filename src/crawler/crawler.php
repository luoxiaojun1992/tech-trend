<?php

use GuzzleHttp\Client;

require_once __DIR__ . '/../vendor/autoload.php';

//todo args

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

$client = new Client();
$cookieJar = new \GuzzleHttp\Cookie\CookieJar();
while(true) {	
	$response = $client->get('https://www.csdn.net/api/articles?type=more&category=home&shown_offset=' . time(), [
		'cookies' => $cookieJar,
	]);

	$jsonData = json_decode($response->getBody()->getContents(), true);
	
	$buffer = '';

	if (isset($jsonData['articles'])) {
		foreach($jsonData['articles'] as $article) {
			//TBD
			/*
			if ($article['id'] > $redis->get('csdn_category_max_id')) {
				$redis->set('csdn_category_max_id', $article['id']);
			} else {
				continue;
			}
			*/
			
			if (!empty($article['tag'])) {
				if ($buffer) {
					$buffer .= (' ' . implode(' ', explode(',', $article['tag'])));
				} else {
					$buffer .= implode(' ', explode(',', $article['tag']));
				}
			}
		}
	}

	if ($buffer) {
		// $redis->lpush('tech_trend:collector_pipeline', $buffer);
		var_dump($buffer);
	}

	sleep(1);
}
