<?php

use GuzzleHttp\Client;

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../common/Helper.php';

$options = \getOptions($argv, ['h', 'p']);
$redisHost = isset($options['h']) ? $options['h'] : '127.0.0.1';
$redisPort = intval(isset($options['p']) ? $options['p'] : 6379);

$redis = new \Redis();
$redis->connect($redisHost, $redisPort);

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
		$redis->lpush('tech_trend:collector_pipeline', $buffer);
	}

	sleep(1);
}
