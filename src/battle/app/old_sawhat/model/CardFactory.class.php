<?php
class Card{
	public $is_private = false;
	public $is_recursive;
	public $color = "#F90";
	public $properties;
	public $last_edit;
	public $name;
	public $text_code;
	public $elements;
	public $files;
	
	public function __construct($name = null, $lines = null, $recursive = true){
		$this->name = $name;
		$this->lines = $lines;
		$lines_count = count($lines);
		$this->is_recursive = $recursive;
		$this->elements = array();
		$init = true;
		$next_is_title = false;
		$element = null;

		for($i = 0; $i < $lines_count; $i++){
			if($init){
				$this->parse_special_properties($lines[$i]);
			}else{
				$this->text_code .= stripslashes($lines[$i]);
				$this->elements[] = new CardElement($name,$this->color,$lines[$i],$recursive);
			}

			if(trim($lines[$i]) == "")
				$init = false;
		}
	}
	
	public function parse_special_properties($line){
		//TODO : IP
		if(preg_match("/lastedit: ([\d]+)/",trim($line),$matches)){
			$date = new DateTime($matches[1]);
			$this->last_edit = $date->format('Y-m-d');
		}
		elseif(preg_match("/color: (#[A-Fa-f0-9]{3,6})/",trim($line),$matches)){
			$this->color = $matches[1];
		}
		elseif(preg_match("/is_private/",trim($line))){
			$this->is_private = true;
		}
	}
	
	public function __toString(){
		$res = "name=".$this->name;
		$res .= "lastedit=".$this->lastedit;
		$res .= "color=".$this->color;
		$res .= "is_private=".$this->is_private;
		foreach($this->elements as $element)
			$res .= $element;
		return '';
	}
}

class CardElement{
	public $html;
	public $cards;
	
	public function __construct($card_name, $card_color, $line, $recursive = true){
		$this->card_name = $card_name;
		
		// parse line ...
		$line = stripslashes($line);

		// links (1/2)
		$line = preg_replace('/\[url=(.+?)\](.*?)\[\/url\]/', '<a style="color:'.$card_color.'" href="$1">$2</a>', $line);
		$line = preg_replace('/\[url\](.*?)\[\/url\]/', '<a style="color:'.$card_color.'" href="$1">$1</a>', $line);
		$line = preg_replace('/ (https?:[\S]+) /', ' <a style="color:'.$card_color.'" href="$1">$1</a> ', $line);
		// images (1/2)
		$line = preg_replace('/\[img=(.*?)\]/', '<img src="$1" alt="" />', $line);
		$line = preg_replace('/\[img\](.*?)\[\/img\]/', '<img src="$1" alt="" />', $line);

		$clean = trim($line);

		// Empty line
		if($clean === ""){
			$this->html = "<br>";
			return;
		}
		// Code
		if(preg_match('/^  (.*)?$/',$line,$matches)){
			$this->html = '<span class="code">'.$matches[1].'</span>';
		}
		// Images (2/2)
		elseif(preg_match('/^(https?:.+\.(?:png|jpg|jpe?g|gif))?$/',$clean,$matches)){
			$this->html = '<img src="'.Request::get_root_url().$matches[1].'" alt="image"/><br>';
		}
		// Links (2/2)
		elseif(preg_match('/^(.*) (https?:[\S]+)$/',$clean,$matches)){
			$this->html = '<a style="color:'.$card_color.'" href="'.$matches[2].'">'.$matches[1].'</a><br>';
		}
		elseif(preg_match('/^(https?:[\S]+)$/',$clean,$matches)){
			$this->html = '<a style="color:'.$card_color.'" href="'.$matches[1].'">'.$matches[1].'</a><br>';
		}
		// Category
		elseif(preg_match('/^\- (.*)$/',$clean,$matches)){
			$this->html = "- ".trim($matches[1], "-")."<br>";
		}
		// Headers/titles
		elseif(preg_match('/^[\-]{2} (.*)$/',$clean,$matches)){
			$this->html = "<h4>".trim($matches[1], "-")."</h4>";
		}
		elseif(preg_match('/^[\-]{3} (.*)$/',$clean,$matches)){
			$this->html = "<h3>".trim($matches[1], "-")."</h3>";
		}
		elseif(preg_match('/^[\-]{4,} (.*)$/',$clean,$matches)){
			$this->html = "<h2>".trim($matches[1], "-")."</h2>";
		}
		// Link to card
		elseif(preg_match('/^\#([\S]*)$/',$clean,$matches)){
			$this->html = '<a style="color:'.$card_color.'" href="'.Request::get_application_virtual_root().$matches[1].'">'.$matches[1].'</a><br>';
		}
		// Local File / Image
		elseif(preg_match('/^\@([\S]*)$/',$clean,$matches)){
			$url = "".CardStore::get_folder().$this->card_name."/".$matches[1];
			if(preg_match('/^\@.+\.(?:png|jpg|jpe?g|gif)?$/',$clean)) 
				$this->html = '<img src="'.Request::get_root_url().$url.'" alt="image"/><br>';
			else
				$this->html = '<a style="color:'.$card_color.'" href="'.Request::get_root_url().$url.'">'.$url.'</a><br>';
		}
		// Cards!
		elseif(preg_match('/^\[([\S]*)\]$/',$clean,$matches) && $recursive == true){
			$cards_names = explode('|', $matches[1]);
			$this->cards = array();
			foreach ($cards_names as $card_name)
				$this->cards[] = CardStore::get($card_name,false);
		}
		// ...
		else{
			$this->html = $clean."<br>";
		}
	}
}
?>