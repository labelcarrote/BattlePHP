<?php
use BattlePHP\Core;
use BattlePHP\Core\Request;
use BattlePHP\View\Form;
use BattlePHP\Storage\Uploader;
use BattlePHP\Storage\FileSystemIO;

class UploadFileForm extends Form{

	const MAX_ITEM_FILE_SIZE = 5242880; // 5Mio 

	public $action = "";
	public $submit_action_name = "upload_file";
	public $max_file_size_human_readable = "";

	public function __construct(){
		$this->max_file_size_human_readable = FileSystemIO::get_human_readable_filesize(self::MAX_ITEM_FILE_SIZE);
		$this->action = Request::get_application_virtual_root()."api";
	}
}
?>