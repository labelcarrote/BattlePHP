<?php
require_once 'core/storage/File.class.php';

/**
 * FileSystemIO
 *
 */
class FileSystemIO{

	/**
	 * Files in $dir folder(s)
	 * Ex: get_files_in_dir("{includes/*.php,core/*.php}");
	 */
	public static function get_files_in_dir($dir){
		$result = array();
		foreach (glob($dir,GLOB_BRACE) as $filename) {
			$file = new File();
			$file->fullname = $filename;
			$file->name = basename($filename);
			$file->size = filesize($filename);
			$result[] = $file;
		}
		return $result;
	}

	public static function get_folders_in_dir($dir){
		$result = array();
		foreach (glob($dir, GLOB_ONLYDIR) as $filename){
			$file = new File();
			$file->fullname = $filename;
			$file->name = basename($filename);
			$result[] = $file;
		}
		return $result;
	}
}
?>
