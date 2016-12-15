<?php
use BattlePHP\Core\Request;
use BattlePHP\Imaging\ImageHelper;
use BattlePHP\Storage\FileSystemIO;
require_once __DIR__.'/DatFile.php';
/********************************************************************
* CLASS DatFileManager
*
* public static methods : 
* - store_dat_file($file_extension, $file_as_base64_string) : DatFile
* - get_dat_file() : DatFile
* - delete_dat_file()
*
*********************************************************************/
class DatFileManager{

	const MAX_FILE_SIZE = 5242880; // 5Mio 
	const DEFAULT_FOLDER = "storage/";
	const DEFAULT_FILENAME = "dat_file";

	public static function store_dat_file($file_extension, $file_as_base64_string){
		self::delete_dat_file();

		if($file_extension === "txt"){
			$new_file_name = @md5_file($file_as_base64_string).".png";
			$data = explode(',', $file_as_base64_string);
			$text = base64_decode($data[1]);
			ImageHelper::create_text_image($text, self::get_dat_file_folder_path().$new_file_name);
		}else{
			$new_file_name = @md5_file($file_as_base64_string).".".$file_extension;
			FileSystemIO::save_file(self::get_dat_file_folder_path().$new_file_name, $file_as_base64_string);
		}

		return self::get_dat_file();
	}

	public static function get_dat_file(){
		$now = new DateTime();
		$dat_file_name = self::get_dat_file_name();
		$url = Request::get_application_root()
			. self::DEFAULT_FOLDER
			. $dat_file_name
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

	// ---- Helpers ----

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