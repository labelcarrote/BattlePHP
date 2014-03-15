<?php
require_once ('app/sawhat/model/CardFactory.class.php');
require_once ('core/storage/FileSystemIO.class.php');

class CardStore{
	
	const DIR = "storage/";
	const EXT = ".txt";
	
	public static function get_folder(){
		return "app/sawhat/".self::DIR;
	}
	
	public static function get_all(){
		$result = array();
		// look for text files in folder sawhat
		$dir = self::get_folder()."*";
		foreach (glob($dir,GLOB_ONLYDIR) as $filename)
			$result[] = self::get(basename($filename));
		return $result;
	}
	
	public static function exist($card_name){
		$card_name = strtolower($card_name);
		$folder = self::get_folder()."$card_name/";
		$filename = $folder.$card_name.".txt";
		return file_exists($filename);
	}

	public static function get($card_name, $recursive = true){
		$card_name = strtolower($card_name);
		$folder = self::get_folder()."$card_name/";
		$filename = $folder.$card_name.".txt";
		if(!file_exists($filename))
			return null;
		
		$lines = file($filename);
		$card = new Card($card_name,$lines,$recursive); 
		$card->files = FileSystemIO::get_files_in_dir($folder.'{*.jpg,*.jpeg,*.JPG,*.png,*.gif}');
		return $card;
	}

	public static function upsert($card_name, $lines, $color = "#f90", $isprivate){
		if(empty($card_name) || empty($lines))
			return;

		$card_name = strtolower($card_name);
		$lastedit = date("Ymd_Hm");
		$filenamenoext = self::get_folder()."".$card_name."/".$card_name;	
		$filename = self::get_folder()."".$card_name."/".$card_name.self::EXT;
		$isprivate_as_string = ($isprivate) ? "isprivate\n" : "";
		if(file_exists($filename)){
			// UPDATE : save current first !
			copy($filename,$filenamenoext."_".$lastedit.self::EXT."old");
		}
		if(!is_dir(dirname($filename)))
			mkdir(dirname($filename));

		$lines = "$card_name\nlastedit: $lastedit\ncolor: $color\n$isprivate_as_string\n".$lines;
		file_put_contents($filename,strip_tags($lines));
	}
}
?>