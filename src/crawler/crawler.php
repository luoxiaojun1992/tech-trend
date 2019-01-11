<?php

$redis = new \Redis();
$redis->connect('127.0.0.1', 6379);

while(true) {	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, 'https://www.csdn.net/api/articles?type=more&category=home&shown_offset=' . time());
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_COOKIEJAR, './cookie.jar');
	curl_setopt($ch, CURLOPT_COOKIEFILE, './cookie.jar');
	$content = curl_exec($ch);
	curl_close($ch);

	$jsonData = json_decode($content, true);
	
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
		$redis->lpush('tech_trend:collector_pipeline', $buffer);
	}

	sleep(1);
}
