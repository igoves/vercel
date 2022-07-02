#!/usr/bin/php
<?php
define("ROOT_DIR", dirname(__DIR__, 1));
$path_parts = pathinfo($_SERVER['SCRIPT_FILENAME']);
chdir($path_parts['dirname']);

$url = 'https://api.privatbank.ua/p24api/exchange_rates?json&date='.date('d.m.Y');
$data = json_decode(file_get_contents($url), true);



$kurs = '';
foreach ( $data['exchangeRate'] as $key => $item ) {
	
	if ( isset($item['currency']) && ( $item['currency'] === 'USD' || $item['currency'] === 'EUR' ) ) {
		$item['saleRate'] = number_format($item['saleRate'], 2, '.', '');
		$item['purchaseRate'] = number_format($item['purchaseRate'], 2, '.', '');
	}
	
	if ( isset($item['currency']) && $item['currency'] === 'USD' ) {
		$kurs .= '$  ' . $item['saleRate'].' - ' . $item['purchaseRate'];
	}

	if ( isset($item['currency']) && $item['currency'] === 'EUR' ) {
		$kurs .= 'E  ' . $item['saleRate'].' - '. $item['purchaseRate'].PHP_EOL;
	}
	if ( isset($item['currency']) && $item['currency'] === 'RUB' ) {
		// $kurs .= 'P  ' . $item['saleRate'].' - '. $item['purchaseRate'].PHP_EOL;
		$kurs .= ''.PHP_EOL;
	}
}

// echo "<pre>";
// print_r(ROOT_DIR);
// print_r($kurs);
// echo "</pre>";
// die();

// @unlink(ROOT_DIR.'/tmp/kurs.jpg');

if ( !empty($kurs) ) {

	require_once ROOT_DIR . '/classes/imageCreate.class.php';

	$text = date('d.m.Y') . PHP_EOL . PHP_EOL . $kurs;

	$image = new ImageCreate($text, $settings = [
		"src"  => ROOT_DIR . "/assets/kurs/bg".rand(1,7).".jpg",
		"size" => 60,
		"top"  => 220,
		"left" => 100,
		"font" => ROOT_DIR . "/assets/fonts/Sumy-Regular.otf",
		"save" => "",
		"name" => ROOT_DIR . "/tmp/kurs.jpg"
	]);
	$path = $image->create();
	echo $path;

	if ( $path ) {

		$botToken = getenv('BOT_TOKEN');
		$chat_id = "@sumy_official";
		$bot_url    = "https://api.telegram.org/bot$botToken/";
		$url        = $bot_url . "sendPhoto?chat_id=" . $chat_id ;

		$post_fields = array('chat_id'   => $chat_id,
			'photo'     => new CURLFile(ROOT_DIR.'/tmp/kurs.jpg'),
		);

		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
			"Content-Type:multipart/form-data"
		));
		curl_setopt($ch, CURLOPT_URL, $url); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields); 
		$output = curl_exec($ch);

		echo $output;
	}

} else {
	echo 'nothing to send';
}

die();