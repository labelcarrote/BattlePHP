<?php
use BattlePHP\Storage\FileSystemIO;
use BattlePHP\Core\Request;

/********************************************************************
* CLASS GoldRater
*
* public static methods : 
* - get_rate()
* - refresh_rate()
*
*********************************************************************/
class GoldRater{

	const GOLD_RATE_SOURCE = "https://www.quandl.com/api/v3/datasets/LBMA/GOLD.json";
	const DEFAULT_FOLDER = "storage/";
	const DEFAULT_FILENAME = "gold.txt";

	public static function get_rate(){
		$rate_data = file_get_contents(Request::get_application_path().self::DEFAULT_FOLDER.self::DEFAULT_FILENAME);
		$rate_data = json_decode($rate_data, false);// object
		return [
			"rate" => $rate_data->dataset->data[0][1],
			"last_update" => $rate_data->dataset->data[0][0]
		]; 
	}

	public static function refresh_rate(){
		FileSystemIO::save_file(Request::get_application_path().self::DEFAULT_FOLDER.self::DEFAULT_FILENAME, self::GOLD_RATE_SOURCE);
	}
}