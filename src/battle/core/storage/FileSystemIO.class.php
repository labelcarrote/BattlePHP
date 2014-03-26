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
			$file->human_readable_size = self::get_human_readable_filesize($file->size);
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
	
	public static function get_human_readable_filesize($bytes, $decimals = 2) {
		// FROM: http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
		$size = array('o','ko','Mo','Go','To','Po','Eo','Zo','Yo');
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . " " .@$size[$factor];
	}

	public static function delete_file($file_path){
		return unlink($file_path);
	}
}
?>
