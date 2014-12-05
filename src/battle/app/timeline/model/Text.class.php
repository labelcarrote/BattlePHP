<?php
require_once 'app/timeline/config/config.php';

/********************************************************************
* CLASS Text 
*
*********************************************************************/
class Text{
	
	const EVENT = "TextAdded";
	const type = "text";

	public $id = "";
	public $date = null;
	public $txt = "";
	public $html = "";

	public function __construct($event = false) {
		if($event){
			$this->id = $event->id;
			$text = json_decode($event->new_value);
			$this->date = $text->date->date;
			$this->txt = $text->txt;
			$this->html = Parsedown::instance()->parse($this->txt);
		}
	}

	public static function create_from_params($params){
		$text = new Text();
		$text->txt = (isset($params['txt'])) ? htmlentities($params['txt'], ENT_QUOTES, 'UTF-8') : "";
		$text->txt = str_replace("\r\n", "<br>", $text->txt);
		$text->date = new DateTime();
		return $text;
	}

	public function to_json(){
		return json_encode($this);
	}
}
?>