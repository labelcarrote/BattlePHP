<?php
require_once 'app/timeline/config/config.php';

/********************************************************************
* CLASS Cigarette 
*
*********************************************************************/
class FAPBattle{
	
	const EVENT = "FAPBattlePublished";
	const type ="fapbattlepublished";

	public $url = "";
	public $date = null;

	public function __construct($event = false) {
		if($event){
			$this->id = $event->id;
			$fapbattle = json_decode($event->new_value);
			$this->date = $fapbattle->date->date;
			$this->url = $fapbattle->url;
		}
	}

	public static function create_from_params($params){
		$fapbattle = new FAPBattle();
		$fapbattle->url = (isset($params['url'])) ? $params['url'] : "";
		$fapbattle->date = new DateTime();
		return $fapbattle;
	}

	public function to_json(){
		return json_encode($this);
	}
}
