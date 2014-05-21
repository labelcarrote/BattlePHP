<?php
/**
 * Request
 *
 * Manage the execution context (from query/url params parsing (REST) to session 
 * and application context)
 * GET, POST, COOKIE, etc ...
 *
 * @author moustachu, touchypunchy
 *
 */
class Request{

	public static function get_application(){
		return $_SESSION['application'];
	}	

    public static function get_root_url(){
        return str_replace('/index.php','',$_SERVER["PHP_SELF"])."/";
    }

    public static function get_application_virtual_root(){
    	if(defined("Configuration::MONO_APP") && Configuration::MONO_APP !== ""){
    		return self::get_root_url();
    	}else{
	    	$sub_domain = explode(".",$_SERVER['HTTP_HOST']);
	    	$sub_domain = $sub_domain[0];

	    	return ($sub_domain !== "" && $sub_domain !== "www" && $sub_domain !== "flipapart" && $sub_domain !== "labelcarrote" && $sub_domain !== "localhost")
	    		? "/"
	    		: self::get_root_url().self::get_application()."/";
    	}
    }

	public static function get_application_root(){
        return self::get_root_url()."app/".self::get_application()."/";
    }

	public static function get_full_url(){
		$protocol = 'http'.(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 's' : '');
		return $protocol.'://'.$_SERVER['HTTP_HOST'];
	}
	
	/**
	 * Parses url param and return param array
	 * or false if error (Sinatra like), ex:
	 * in a action method named "truc"
	 * $params = get_params("/@username/rien/@projectname") // -> url = truc/jon/rien/poneysize
	 * echo $params["username"]; // jon !
	 * echo $params["projectname"]; // poneysize !
	 *
	 * TODO : remove last "/" if exist before explode
	 * TODO : CLARIFY parameters shift in mono / multi app scenario
	 */
	public static function get_params($pattern){
		$result = false;
		$params = explode('/',$_SESSION['param']);
		$expected_params = explode('/',$pattern);
		if($_SESSION['param'] == "")
			return $result;
		if(count($params) == count($expected_params)){
			for($i = 0; $i < count($params); $i++){
				if(preg_match('/^@([a-zA-Z0-9_-]+)$/',$expected_params[$i],$matches))
					$result[$matches[1]] = $params[$i];
			}
		}
		return $result;
	}

	public static function set_params($value){
		$_SESSION['param'] = $value;
	}

	/**
	 * If set, returns the value of parameter,
	 * otherwise returns the default value.
	 */
	public static function isset_or(&$check, $default = NULL){
		return (isset($check)) ? $check	: $default;
	}

	/**
	 * Passes given query parameter to session.
	 */
	public static function from_query_to_session($name, $default){
		if(isset($_GET[$name]))
			$_SESSION[$name] = $_GET[$name];
		else if(isset($_POST[$name]))
			$_SESSION[$name] = $_POST[$name];
		else
			$_SESSION[$name] = $default;
		return $_SESSION[$name];
	}
}
?>