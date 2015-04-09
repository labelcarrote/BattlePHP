<?php
require_once 'app/timeline/config/config.php';

/********************************************************************
* CLASS Cigarette 
*
*********************************************************************/
class Cigarette{
	
	const EVENT = "CigaretteSmoked";
	const type ="cigarette";

	public $id = "";
	public $date = null;
	public $excuse = "";

	public function __construct($event = false) {
		if($event){
			$this->id = $event->id;
			$cigarette = json_decode($event->new_value);
			$this->date = $cigarette->date->date;
			$this->excuse = $cigarette->excuse;
		}
	}

	public static function create_from_params($params){
		$cigarette = new Cigarette();
		$cigarette->excuse = (isset($params['excuse'])) ? $params['excuse'] : "";
		$cigarette->date = new DateTime();
		return $cigarette;
	}

	public function to_json(){
		return json_encode($this);
	}
}
?>