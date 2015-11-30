<?php
use BattlePHP\Core;
use BattlePHP\View\Form;
use BattlePHP\Core\Request;
use BattlePHP\Storage\FileSystemIO;
require_once __DIR__.'/DatFileManager.php';
/********************************************************************
* CLASS UploadFileForm (Form)
*
*********************************************************************/
class UploadFileForm extends Form{

	public $action = "";
	public $submit_action_name = "upload_file";
	public $max_file_size_human_readable = "";

	public function __construct(){
		$this->max_file_size_human_readable = FileSystemIO::get_human_readable_filesize(DatFileManager::MAX_FILE_SIZE);
		$this->action = Request::get_application_virtual_root()."api";
	}
}