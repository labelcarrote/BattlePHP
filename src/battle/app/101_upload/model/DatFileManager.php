<?php
use BattlePHP\Core\Request;
use BattlePHP\Storage\FileSystemIO;
require_once __DIR__.'/DatFile.php';
/**
 * CLASS DatFileManager
 */
class DatFileManager{

	const DEFAULT_FOLDER = "storage/";
	const DEFAULT_FILENAME = "dat_file";

	public static function store_dat_file($file_extension, $file_as_base64_string){
		self::delete_dat_file();
		$now = new DateTime();
		$new_file_name = @md5_file($file_as_base64_string).".".$file_extension;
		file_put_contents(self::get_dat_file_folder_path().$new_file_name, file_get_contents($file_as_base64_string));
		return self::get_dat_file();
		//return Request::get_application_root().self::DEFAULT_FOLDER.$new_file_name."#".$now->format("His");
	}

	public static function get_dat_file(){
		$now = new DateTime();
		$dat_file_name = self::get_dat_file_name();
		$url = Request::get_application_root()
			. self::DEFAULT_FOLDER
			. self::get_dat_file_name()
			. "?"
			. $now->format("His");
		$dat_file_path = self::get_dat_file_folder_path().$dat_file_name;
		$date_modified = new DateTime();
		$date_modified->setTimestamp(filemtime ($dat_file_path));
		return new DatFile($url,$date_modified);
	}

	public static function delete_dat_file(){
		FileSystemIO::delete_file(self::get_dat_file_path());
	}

	private static function get_dat_file_name(){
		$files = FileSystemIO::get_files_in_dir(self::get_dat_file_folder_path()."*");
		if(count($files) > 0)
			return $files[0]->name;
		return false;
	}

	private static function get_dat_file_path(){
		return self::get_dat_file_folder_path().self::get_dat_file_name();
	}

	private static function get_dat_file_folder_path(){
		return Request::get_application_path().self::DEFAULT_FOLDER;
	}
}