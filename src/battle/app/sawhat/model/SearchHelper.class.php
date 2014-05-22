<?php
class SearchHelper{
	const MAX_ITEM = 5;
	const BREADCRUMBS_SESSION_VAR = 'NavigationHelper';
	const DEFAULT_NAME = 'breadcrumbs';

	/* 
	 * Cleans keywords before search request
	 *
	 * @param string $string_request
	 * @param integer $min_char_per_word
	 * @return string 
	 *
	 */
	public static function prepare_request($string_request, $min_char_per_word = 3){
		$request = ' '.preg_replace('/[^a-zA-Z0-9 _-]/',' ',$string_request).' ';
		$clean_pattern = '/ [a-zA-Z0-9_-]{1,'.($min_char_per_word-1).'} /';
		while(preg_match($clean_pattern,$request)){
			$request = preg_replace($clean_pattern,' ',$request);
		}
		return preg_replace('/ +/',' ',trim($request));
	}

	public static function explode_keywords($keywords, $delimiter = ' '){
		$rtn = array('in'=>array(),'out'=>array());
		$keywords = explode($delimiter,$keywords);
		foreach($keywords as $keyword){
			if(strpos($keyword,'-') === 0){
				$rtn['out'][] = substr($keyword,1);
			} else {
				$rtn['in'][] = $keyword;
			}
		}
		
		return $rtn;
	}
	
	public static function keyword_in_file($keyword,$file_path){
		if(is_file($file_path) && $handle = fopen($file_path,'r')){
			while(($line = fgets($handle,4096)) !== false){
				if(self::keyword_in_line(strtolower($keyword),' '.strtolower(self::clean_newline($line)).' ')){
					return true;
				}
			}
			fclose($handle);
		}
		
		return false;
	}
	
	public static function keyword_in_line($keyword,$string){
		return preg_match('/[\W]'.preg_quote($keyword, '/').'[\W]/',$string);
	}
	
	private static function clean_newline($string){
		$s = array("\r","\n");
		$r = array(' ',' ');
		return str_replace($s,$r,$string);
	}
}
?>