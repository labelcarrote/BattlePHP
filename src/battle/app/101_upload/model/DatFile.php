<?php
use BattlePHP\Core\Request;
use BattlePHP\Storage\FileSystemIO;
/**
 * CLASS DatFile
 */
class DatFile{

	public $url = "";
	public $date_modified = "";

	public function __construct($url,$date_modified){
		$this->url = $url;
		$this->date_modified = $date_modified;
	}
}