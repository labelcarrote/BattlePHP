<?php
use BattlePHP\Core\Request;
use BattlePHP\Storage\FileSystemIO;
/**
 * 
 */
class DatFileManager{

	const DEFAULT_FOLDER = "storage/";
	const DEFAULT_FILENAME = "dat_file";

	public static function store_dat_file($file_extension, $file_bytes){
		$new_file_name = DatFileManager::DEFAULT_FOLDER.DatFileManager::DEFAULT_FILENAME.$file_extension;
		file_put_contents($new_file_name, file_get_contents($data->file));
		return Request::get_application_root().$new_file_name;
	}

	public static function get_dat_file_url(){
		$extension = strtolower(pathinfo(self::get_dat_file_name(),PATHINFO_EXTENSION));
		$now = new DateTime();
		return Request::get_application_root()
			. DatFileManager::DEFAULT_FOLDER
			. DatFileManager::DEFAULT_FILENAME
			. "."
			. $extension
			. "#"
			. $now->format("His");
			//. self::get_dat_file_name();
			/*. DatFileManager::DEFAULT_FILENAME
			. $file_extension*/
	}

	public static function get_dat_file_name(){
		$files = FileSystemIO::get_files_in_dir("app/101_upload/".self::DEFAULT_FOLDER."*");
		if(count($files) > 0)
			return $files[0]->name;
		return false;
	}
}