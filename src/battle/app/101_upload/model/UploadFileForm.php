<?php
use BattlePHP\Core;
use BattlePHP\Core\Request;
use BattlePHP\View\Form;
use BattlePHP\Storage\Uploader;
use BattlePHP\Storage\FileSystemIO;

class UploadFileForm extends Form{

	const FILE_NAME = "dat_file";
	const MAX_ITEM_FILE_SIZE = 5242880; // 5Mio 

	public $action = "";
	public $submit_action_name = "upload_file";
	public $max_file_size_human_readable = "";

	// TODO : File instance ?
	public $file_name;

	public function __construct(){
		$this->max_file_size_human_readable = FileSystemIO::get_human_readable_filesize(self::MAX_ITEM_FILE_SIZE);
		$this->action = Request::get_application_virtual_root()."api";
	}

	public function get_file_url(){
		$file_folder = "";
		if($this->file_name != "")
			return Request::get_root_url().$file_folder.$this->file_name;
		return "#";
	}

	public function submit_cover_upload(){
		$this->process_form_files_upload();
		if($this->validate()){
			return $this->save_cover();
		}
		return false;
	}

	private function process_form_files_upload(){
		if(isset($_FILES["cover"]) && !empty($_FILES["cover"]["name"])){
			try{
				$prefix = "cover_{$this->user_id}_";
				$this->file_name = 
					Uploader::process_form_file(
						"cover",
						$this->profile->get_avatar_folder(),
						self::MAX_ITEM_FILE_SIZE,Uploader::get_img_extensions(), 
						$prefix
					);
			}
			catch(Exception $e){
				$this->add_error("upload", Core\i18n_get("profile.avatar_uploaded_incorrect"));
			}
		}else{
			$this->add_error("cover", Core\i18n_get("battle.no_file_uploaded"));	
		}
	}

	private function validate(){	
		return (count($this->errors) === 0);
	}

	private function save_cover(){
		// delete old cover 
		if($this->old_cover_name !== ""){
			$old_cover_path = $this->profile->get_avatar_folder()."/".$this->old_cover_name; 
			FileSystemIO::delete_file($old_cover_path);
		}
		
		return true;//ProfileManager::save_profile($this->profile);
	}
}
?>