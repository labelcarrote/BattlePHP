<?php
namespace BattlePHP\String;
use HTMLPurifier;
use HTMLPurifier_Config;

class SanitizationHelper{
	public static function sanitize($dirty_string){
		$config = HTMLPurifier_Config::createDefault();
		$purifier = new HTMLPurifier($config);
		return $purifier->purify($dirty_string);
	}
}
