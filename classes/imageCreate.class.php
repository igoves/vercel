<?php
class ImageCreate
{
	/**
	* @var $settings - НАСТРОЙКИ
	* src  - Путь к изображению, на которое нанесём текст
	* size - Размер шрифта
	* top  - Отступ сверху
	* left - Отступ слева
	* font - Путь к файлу шрифта
	* save - Путь для сохранения
	*/
	private $settings = [];
	
	/**
	* 
	* @var Содержит пользовательский текст
	* 
	*/
	private $text;
	
	/**
	* 
	* @param пользовательский текст $text
	* 
	*/
	public function __construct($text, $settings){
		$this->text = $text;
		$this->settings = $settings;
	}
	
	/**
	*
	* @return путь к созданному изображению
	* 
	*/
	public function create()
	{
		# Открываем рисунок в формате JPEG
		$img = imagecreatefromjpeg($this->settings["src"]);
		
		# Получаем идентификатор цвета
		$color = imagecolorallocate($img, 255, 255, 255);
	  
		/* выводим текст на изображение */
		imagettftext(
			$img, 
			$this->settings["size"], 
			0, 
			$this->settings["left"], 
			$this->settings["top"], 
			$color, 
			$this->settings["font"],
			$this->to_entities($this->text)
		);
		
		# Генерируем путь для сохранения
		$path = $this->settings["save"] . $this->settings["name"];
		
		# Сохраняем рисунок в формате JPEG
		imagejpeg($img, $path, 100);
		
		# Освобождаем память и закрываем изображение
		imagedestroy($img);
		
		# Возвращаем путь
		return $path;
	}
	
	
	function to_entities($string){
		$len = strlen($string);
		$buf = "";
		for($i = 0; $i < $len; $i++){
			if (ord($string[$i]) <= 127){
				$buf .= $string[$i];
			} else if (ord ($string[$i]) <192){
				//unexpected 2nd, 3rd or 4th byte
				$buf .= "&#xfffd";
			} else if (ord ($string[$i]) <224){
				//first byte of 2-byte seq
				$buf .= sprintf("&#%d;",
					((ord($string[$i + 0]) & 31) << 6) +
					(ord($string[$i + 1]) & 63)
				);
				$i += 1;
			} else if (ord ($string[$i]) <240){
				//first byte of 3-byte seq
				$buf .= sprintf("&#%d;",
					((ord($string[$i + 0]) & 15) << 12) +
					((ord($string[$i + 1]) & 63) << 6) +
					(ord($string[$i + 2]) & 63)
				);
				$i += 2;
			} else {
				//first byte of 4-byte seq
				$buf .= sprintf("&#%d;",
					((ord($string[$i + 0]) & 7) << 18) +
					((ord($string[$i + 1]) & 63) << 12) +
					((ord($string[$i + 2]) & 63) << 6) +
					(ord($string[$i + 3]) & 63)
				);
				$i += 3;
			}
		}
		return $buf;
	}
	
}