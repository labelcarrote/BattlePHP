<?php
use BattlePHP\View\Form;
use BattlePHP\Core\Request;
use BattlePHP\Storage\FileSystemIO;
/********************************************************************
* CLASS UploadFileForm (Form)
*
*********************************************************************/
class UploadFileForm extends Form{

	public $action = "";
	public $submit_action_name = "upload_file";
	public $max_file_size = 5242880;
	public $max_file_size_human_readable = "";
	public $card_name = "";

	public function __construct($submit_action_name = null, $card_name = null, $max_file_size = null){
		if($submit_action_name !== null)
			$this->submit_action_name = $submit_action_name;
		if($card_name !== null)
			$this->card_name = $card_name;
		if($max_file_size !== null)
			$this->max_file_size = $max_file_size;

		$this->max_file_size_human_readable = FileSystemIO::get_human_readable_filesize($this->max_file_size);
		$this->action = Request::get_application_virtual_root()."api";
	}
}