<?php
/**
 * 
 */
class DatFileManager{

	const DEFAULT_FOLDER = "app/101_upload/storage/";
	const DEFAULT_FILENAME = "dat_file";

	public static function store_dat_file($file_extension, $file_bytes){
		$new_file_name = DatFileManager::DEFAULT_FOLDER.DatFileManager::DEFAULT_FILENAME.$file_extension;
		file_put_contents($new_file_name, file_get_contents($data->file));
		return Request::get_application_root().$new_file_name;
	}

	public static function get_dat_file_url(){
	
	}

	public static function get_dat_file_name(){
		$files = FileSystemIO::get_files_in_dir(self::DEFAULT_FOLDER."*");
		if(count($files) > 0)
			return $files[0]->name;
		return false;
	}
}