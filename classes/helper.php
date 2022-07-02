<?php
class h {
	
	private function getToken () {
		
		$url_auth = 'https://api.telegra.ph/createAccount?short_name=Новости+Сумы+😱&author_name=Новости+Сумы+😱&author_url=https://t.me/sumy_official';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_auth );
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT , 7);
		curl_setopt($ch, CURLOPT_USERAGENT , "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");
		curl_setopt($ch, CURLOPT_HEADER, 0);
		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($result, true);

		if ( !$result['ok']['error'] ) {
			return $result['result']['access_token'];
		} else {
			print_r($result);
			die();
		}
		
	}
	
	
	public static function getLink ($dataArray) {
		
		$title = $dataArray['title'];
		$content = $dataArray['content'];
		$token = self::getToken();

		$data = http_build_query([
			'access_token' => $token,
			'title' => $title,
			'author_name' => 'Новости Сумы 😱',
			'author_url' => 'https://t.me/sumy_official',
			'content' => json_encode($content),
			'return_content' => 'true'
		]);

		$url_createPage = 'https://api.telegra.ph/createPage';

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url_createPage );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
		$result = curl_exec($ch);
		curl_close($ch);

		$result = json_decode($result, true);

		if ( $result['ok'] == 1 ) {
			return $result['result']['url'];
		} else {
			die('Error: '.$result['error']);
		}  

	}

	
	public static function sendMessage ($message) {
		
		$botToken = "5427698339:AAEmuguGnHOkuiIs4nskCQ0uEQulTPNwGBo";
		$chat_id = "@sumy_official";
		$bot_url    = "https://api.telegram.org/bot$botToken/";
		$url = $bot_url."sendMessage?chat_id=".$chat_id."&text=".urlencode($message)."&parse_mode=HTML";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		echo curl_exec($ch);
		curl_close($ch); 
		
	}
	
}