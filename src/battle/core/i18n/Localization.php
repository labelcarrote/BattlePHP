<?php
// ---- I18N ----
//http://www.schittkowski.de/?q=node/20

function i18n_init(){
	global $i18n_tokens;
	$application = Request::isset_or($_SESSION["application"]);
	$f = 'app/'.$application.'/i18n/lang_' . i18n_get_language() . '.php';
	if (file_exists($f) && is_readable($f)) {
		$s = file_get_contents($f);
		eval($s);
	}
}

// language which is selected by user (defined in the browser settings)
function i18n_get_language(){
	$lang_variable = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	if (empty($lang_variable))
		$lang_variable = 'en'; 
	return $lang_variable;
}

// get the translated token (ex: i18n_get("nav_tournaments") -> TOURNAMENT (english))
function i18n_get($token){ 
	global $i18n_tokens;
	$a = $token;
	//found
	if (isset($i18n_tokens[$a]))
		return $i18n_tokens[$a];
	else {
		//not found, new entry in language file is created
		$f = 'i18n/lang_' . i18n_get_language() . '.php';        
		if (file_exists($f) && is_writeable($f)) {
			$s = file_get_contents($f);
			eval($s);
		} else {
			//echo "No file access for" . $f . " von " . __FILE__ . "<br>";
			return $token;
		}    
		if (isset($i18n_tokens[$a]))
			return $i18n_tokens[$a];  

		$i18n_tokens[$a] = $a;       
		$s = '$i18n_tokens' . "['" . $a . "']" . "='" . $a . "'; //neu" . PHP_EOL;
		if ($handle = fopen($f, 'a')) {                
			fwrite($handle, $s);   
			fclose($handle);                
		}                    
		return $a;
	}
}      	

// substitutes a single token
function i18n_substitute_text_token($token) {
	global $i18n_tokens;
	$a = trim($token[1]);
	//found
	if (isset($i18n_tokens[$a]))
		return $i18n_tokens[$a];
	else {
		//not found, new entry in language file is created
		$f = 'i18n/lang_' . i18n_get_language() . '.php';        
		if (file_exists($f) && is_writeable($f)) {
			$s = file_get_contents($f);
			eval($s);
		} else {
			echo "No file access for" . $f . " von " . __FILE__ . "<br>";
			return $token;
		}    
		if (isset($i18n_tokens[$a]))
			return $i18n_tokens[$a];  

		$i18n_tokens[$a] = $a;       
		$s = '$i18n_tokens' . "['" . $a . "']" . "='" . $a . "'; //neu" . PHP_EOL;
		if ($handle = fopen($f,'a')){
			fwrite($handle, $s);
			fclose($handle);
		}
		return $a;
	}
}

// Smarty-output filter, it reads a file and parses it contents
// all contents starting and ending with @@ are replaced
function i18n_substitute_text($tpl_output, &$smarty){
	i18n_init();
	return preg_replace_callback('/@@(.+?)@@/', 'i18n_substitute_text_token', $tpl_output);       
}
?>