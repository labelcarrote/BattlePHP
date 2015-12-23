<?php
use BattlePHP\Core\Request;
/********************************************************************
* CLASS DatFile
*********************************************************************/
class DatFile{

	public $url = "";
	public $absolute_url = "";
	public $extension = "";
	public $date_modified = "";

	public function __construct($url,$date_modified){
		$this->url = $url;
		$this->absolute_url = Request::get_full_url().substr($url, 0, strpos($url, "?"));
		$this->extension = strtolower(pathinfo($url,PATHINFO_EXTENSION));
		$this->extension = substr($this->extension, 0, strpos($this->extension, "?"));
		$this->date_modified = $date_modified;
	}
}
