<?php
require_once 'app/timeline/config/config.php';

/********************************************************************
* CLASS Picture 
*
*********************************************************************/
class Picture{

	const EVENT = "PictureAdded";
	const type = "picture";

	public $id = "";
	public $file_name = null;
	public $width = "";
	public $height = "";
	public $size = "";
	public $date = null;

	public function __construct($event = false) {
		$this->date = new DateTime();
		if($event){
			$this->id = $event->id;
			$picture = json_decode($event->new_value);
			$this->file_name = $picture->file_name;
			$this->width = $picture->width;
			$this->height = $picture->height;
			$this->size = $picture->size;
			$this->date = $picture->date->date;
		}
	}

	public function get_folder(){
		return ConfigurationTimeline::PICTURE_STORAGE_FOLDER;
	}

	public function get_path(){
		return ConfigurationTimeline::PICTURE_STORAGE_FOLDER."{$this->file_name}";
	}

	public function to_json(){
		return json_encode($this);
	}
}
