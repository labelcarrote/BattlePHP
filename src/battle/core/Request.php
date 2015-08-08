<?php
namespace BattlePHP\Core;
use Configuration;

/**
 * Request
 *
 * Manage the execution context (from query/url params parsing (REST) to session 
 * and application context)
 * GET, POST, COOKIE, etc ...
 *
 * TODO : update naming to reflect difference between url / path etc 
 * cf : http://webhelp.esri.com/arcgisdesktop/9.2/index.cfm?TopicName=Pathnames_explained%3A_Absolute%2C_relative%2C_UNC%2C_and_URL
 *
 * @author moustachu, touchypunchy
 *
 */
class Request{
	
	public static function get_current_application(){
		return Request::isset_or($_SESSION['application']);
	}	
	public static function set_current_application($application_name){
		$_SESSION['application'] = $application_name;
	}	

	public static function get_current_controller(){
		return Request::isset_or($_SESSION['controller']);
	}	
	public static function set_current_controller($controller_name){
		$_SESSION['controller'] = $controller_name;
	}	

	public static function get_current_action(){
		return Request::isset_or($_SESSION['action']);
	}	
	public static function set_current_action($action_name){
		$_SESSION['action'] = $action_name;
	}

	public static function get_current_params(){
		return Request::isset_or($_SESSION['param']);
	}	
	public static function set_current_params($param_name){
		$_SESSION['param'] = $param_name;
	}	

	/**
	 * Parses url param and return param array
	 * or false if error (Sinatra like), ex:
	 * in a action method named "truc"
	 * $params = get_params("/@username/rien/@projectname") // -> url = truc/jon/rien/poneysize
	 * echo $params["username"]; // jon !
	 * echo $params["projectname"]; // poneysize !
	 */
	public static function get_params($pattern){
		// TODO: remove last "/" if exist before explode ??
		$result = false;
		$params = explode('/',self::get_current_params());
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

	public static function get_root_url(){
		return str_replace('/index.php','',$_SERVER["PHP_SELF"])."/";
	}

	public static function get_application_virtual_root(){
		if(defined("Configuration::MONO_APP") && Configuration::MONO_APP !== ""){
			return self::get_root_url();
		}else{
			$sub_domain = explode(".",$_SERVER['HTTP_HOST']);
			$sub_domain = $sub_domain[0];

			// TEMP : 192 !!
			return ($sub_domain !== "" && $sub_domain !== "www" && $sub_domain !== "flipapart" && $sub_domain !== "labelcarrote" && $sub_domain !== "localhost" && $sub_domain !== "192")
				? "/"
				: self::get_root_url().self::get_current_application()."/";
		}
	}

	public static function get_application_root(){
		return self::get_root_url()."app/".self::get_current_application()."/";
	}

	public static function get_application_path(){
		return $_SERVER['DOCUMENT_ROOT'].self::get_root_url().'/app/'.self::get_current_application().'/';
	}

	public static function get_full_url(){
		//TODO : HTTPS 
		return "http://{$_SERVER['HTTP_HOST']}";
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

	/**
	 * Mobile device detection (from http://detectmobilebrowsers.com/)
	 */
	public static function is_mobile_device(){
		/*return true;*/
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		return preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i',substr($useragent,0,4));
	}
}
