<?php 
namespace BattlePHP\Core;
use Configuration;
use ReflectionClass;
use ReflectionException;
/**
 * Router : 
 * Redirects execution to an action (in a controller) from the query parameters 
 * in current Request
 * @author touchypunchy
 */
class Router{

	// ---- Static fields ----

	const DEFAULT_APPLICATION = '';
	const DEFAULT_CONTROLLER = 'home';
	const DEFAULT_ACTION = 'index';

	// ---- Public methods ----
	
	/**
	 * Run : redirects to the action specified in query and runs it
	 */
	public static function run(){
		// 1 - construct Request
		// MONO APP
		if(defined("Configuration::MONO_APP") && Configuration::MONO_APP !== ""){
			Request::set_current_application(Configuration::MONO_APP);
			Request::set_current_controller(
				Request::isset_or($_GET["param1"],self::DEFAULT_CONTROLLER));
			Request::set_current_action(
				Request::isset_or($_GET["param2"],self::DEFAULT_ACTION)
			);
			Request::set_current_params(
				Request::isset_or($_GET["param3"],null)
			);
		}
		// MULTIPLE APPS
		else{
			Request::set_current_application(
				Request::isset_or($_GET['param1'],self::DEFAULT_APPLICATION)
			);
			Request::set_current_controller(
				Request::isset_or($_GET["param2"],self::DEFAULT_CONTROLLER)
			);
			Request::set_current_action(
				Request::isset_or($_GET["param3"],self::DEFAULT_ACTION)
			);
			Request::set_current_params(
				Request::isset_or($_GET["param4"],null)
			);
		}
		
		if(Request::get_current_params() === null){
			$sinatra_params = Request::get_current_controller();
			$sinatra_params .= (Request::get_current_action() != self::DEFAULT_ACTION) 
				? "/".Request::get_current_action() 
				: "";
			Request::set_current_params($sinatra_params);
		}
		
		//self::debug();

		// 2 - execute Request
		// If no app specified, show root index.tpl
		if(empty(Request::get_current_application())){
			Viewer::display_root();
			return;
		}
		$is_action_executed = self::go_to_action(
			Request::get_current_controller(),
			Request::get_current_action(),
			Request::get_current_application()
		);

		if(!$is_action_executed){
			$is_action_executed = self::go_to_default_action(
				Request::get_current_controller(),
				Request::get_current_application()
			);
		}

		if(!$is_action_executed){
			// If specified controller not found
			$controller_folder = "app/".Request::get_current_application()."/action/";
			$any_other_controller_found = false;
			//try to find any controller
			foreach(glob("{$controller_folder}Action*.class.php") as $filename){
				$controller_class_file_path = $filename;
				$split = explode("/",$filename);
				$controller_name = $split[count($split) - 1];
				$controller_name = str_replace(array("Action",".class.php"),"",$controller_name);

				$split = explode(".", $controller_name);
				$controller_class = $split[0];
				$any_other_controller_found = true;
				break;
			}
			// If no controller found, try to find a default view to display (in the app's view folder).
			// First look for "section.$controller.tpl"
			if(!$any_other_controller_found){
				self::go_to_default_view();
			}
			else{
				self::go_to_action(
					$controller_name,
					Request::get_current_action(),
					Request::get_current_application()
				);
			}
		}
	}

	/**
	 * Loads and runs action in given controller and application, return false if not found
	 */
	public static function go_to_action($controller,$action,$application = null, $errors = null){
		// Sets the action class file path
		$controller_class = 'Action'.ucfirst($controller);
		$controller_folder = "app/$application/action/";
		$controller_class_file_path = $controller_folder.$controller_class.'.class.php';

		// Loads the file
		if(file_exists($controller_class_file_path))
			require_once($controller_class_file_path);

		if (!class_exists($controller_class,false))
			return false;

		// If controller class was found (and not autoloaded!)
		$class = new ReflectionClass($controller_class);
		$instance = $class->newInstance($errors);
		if(!$class->hasMethod($action))
			return false;

		$method = $class->getMethod($action);
		$method->invoke($instance);
		return true;
	}

	/**
	 * Loads and runs default action of default controller and current application (if no controller or application given), return false if not found
	 */
	public static function go_to_default_action($controller = null,$application = null,$errors = null){
		if($controller === null)
			$controller = self::DEFAULT_CONTROLLER;
		if($application === null)
			$application = Request::get_current_application();

		// Sets the action class file path
		$controller_class = 'Action'.ucfirst(self::DEFAULT_CONTROLLER);
		$controller_folder = "app/{$application}/action/";
		$controller_class_file_path = $controller_folder.$controller_class.'.class.php';

		// Loads the file
		if(file_exists($controller_class_file_path))
			require_once($controller_class_file_path);

		if (!class_exists($controller_class,false))
			return false;
		
		// If controller class was found (and not autoloaded!)
		// Create and invoke an instance of the default action
		$class = new ReflectionClass($controller_class);
		$instance = $class->newInstance($errors);
		if(!$class->hasMethod(self::DEFAULT_ACTION))
			return false;

		$method = $class->getMethod(self::DEFAULT_ACTION);
		$method->invoke($instance);

		return true;
	}

	/**
	 * Display default view of current controller and current application, return false if not found
	 */
	public static function go_to_default_view($errors = null){
		$tpl_folder = "app/".Request::get_current_application()."/view/";
		$tpl_name = "section.".Request::get_current_controller().".tpl";
		$tpl_path = $tpl_folder.$tpl_name;
		if(!file_exists($tpl_path)){
			$tpl_found = false;
			foreach(glob("$tpl_folder*.tpl") as $filename){
				$tpl_found = true;
				$split = explode("/",$filename);
				$tpl_name = $split[count($split) - 1];
				break;
			}
			if(!$tpl_found){
				echo "No pages were found!";
				return;
			}
		}
		Viewer::display_result($tpl_name,false);
	}

	/**
	* Returns the list of all the app in the app folder (except the one starting with "_")
	*/
	public static function get_all_apps(){
		$all_apps = array();
		foreach (glob("app/*",GLOB_ONLYDIR) as $dirname){
			$split = explode("/",$dirname);
			$shortdir = $split[count($split) - 1];
			if(substr($shortdir, 0, 1) == "_")
				continue;
			$all_apps[] = $shortdir;
		}
		return $all_apps;
	}

	/**
	* Debug 
	*/
	public static function debug(){
		$debug = "";
		if(defined("Configuration::MONO_APP") && Configuration::MONO_APP !== ""){
			$debug .= "MONO APP<br>";
		}
		// MULTIPLE APPS
		else{
			$debug .= "MULTI APP<br>";
		}
		$debug .= "[application] = ".Request::get_current_application()."<br>";
		$debug .= "[controller] = ".Request::get_current_controller()."<br>";
		$debug .= "[action] = ".Request::get_current_action()."<br>";
		$debug .= "[param] = ".Request::get_current_params()."<br>";
		echo $debug;
	}
}
