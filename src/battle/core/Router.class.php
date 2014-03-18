<?php
/**
 * Router : 
 * Redirect execution to an action (in a controller) from the query parameters 
 * in current "Request"
 * @author touchypunchy
 */
class Router{

	// ---- Static fields ----

	const DEFAULT_APPLICATION = '';
	const DEFAULT_CONTROLLER = 'home';
	const DEFAULT_ACTION = 'index';

	// ---- Public methods ----
	
	/**
	 * Run : redirect to action specified in query and run it
	 */
	public static function run(){
		// MONO APP
		if(defined("Configuration::MONO_APP") && Configuration::MONO_APP !== ""){
			$_SESSION["application"] = $application = Configuration::MONO_APP;
			$_SESSION["controller"] = $controller = Request::isset_or($_GET["application"],self::DEFAULT_CONTROLLER);
			$_SESSION["action"] = $action = Request::isset_or($_GET["controller"],self::DEFAULT_ACTION);
			$_SESSION["param"] = $param =  Request::isset_or($_GET["action"],null);
			self::go_to_action($controller,$action,$application);
		}
		// MULTIPLE APPS
		else{
			$application = Request::from_query_to_session('application',self::DEFAULT_APPLICATION);
			$controller = Request::from_query_to_session('controller',self::DEFAULT_CONTROLLER);
			$action = Request::from_query_to_session('action',self::DEFAULT_ACTION);
			Request::from_query_to_session('param',null);
			self::go_to_action($controller,$action,$application);
		}
	}

	/**
	 * Loads and runs action in given controller and application (if specified, otherwise display root view)
	 */
	public static function go_to_action($controller,$action,$application = null, $errors = null){
		// If no app specified, show root index.tpl
		if(empty($application)){
			Viewer::display_root();
			return;
		}
		// Sets the action class file path
		$controller_class = 'Action'.ucfirst($controller);
		$controller_folder = "app/$application/action/";
		$controller_class_file_path = $controller_folder.$controller_class.'.class.php';

		// Loads the file
		if(file_exists($controller_class_file_path))
			require_once($controller_class_file_path);

		// If controller class was found (and not autoloaded!)
		if (class_exists($controller_class,false)) {
			try{
				// Create and invoke an instance of the action requested
				$class = new ReflectionClass($controller_class);
				$instance = $class->newInstance($errors);
				$method = $class->getMethod($action);
				$method->invoke($instance);
			}catch (Exception $error) {
				// If action doesn't have a corresponding method in class, then
				// try to invoke the default method (index ?)
				try{
					$method = $class->getMethod(self::DEFAULT_ACTION);
					$method->invoke($instance);
				}
				catch(Exception $error_nodefaultaction){
					Logger::trace(__CLASS__.".instanciation action error: $action");
					die($error->getMessage());
				}
			}
		}else{
			$found = false;
			// If specified controller not found try to find a default controller
			foreach(glob("$controller_folder*.class.php") as $filename){
				$controller_class_file_path = $filename;
				$split = explode("/",$filename);
				$controller_name = $split[count($split) - 1];
				$split = explode(".", $controller_name);
				$controller_class = $split[0];
				$found = true;
				break;
			}
			if($found){
				// Load the file
				if(file_exists($controller_class_file_path))
					require_once($controller_class_file_path);

				if (class_exists($controller_class,false)) {
					try{
						// Create and invoke an instance of the action requested
						$class = new ReflectionClass($controller_class);
						$instance = $class->newInstance($errors);
						//given controller become action 
						$method = $class->getMethod($controller);
						$method->invoke($instance);
					}catch (Exception $error) {
						// If action doesn't have a corresponding method in class, then
						// try to invoke the default method (ex: "index")
						try{
							//shift "REST url params in query" to the left to make action becomes params
							$newparams = $controller;
							$newparams .= ($action != self::DEFAULT_ACTION) ? "/".$action : "";
							Request::set_params($newparams);
							$method = $class->getMethod(self::DEFAULT_ACTION);
							$method->invoke($instance);
						}
						catch(Exception $error_nodefaultaction){
							Logger::trace(__CLASS__.".instanciation action error: $action");
							die($error->getMessage());
						}
					}
				}
			}
			// If no controller found, try to find a default view to display (in the app's view folder).
			// First look for "section.$controller.tpl"
			else{
				
				$tpl_folder = "app/$application/view/";
				$tpl_name = "section.$controller.tpl";
				$tpl_path = $tpl_folder.$tpl_name;
				if(!file_exists($tpl_path)){
					// Look for any "*.tpl"
					foreach(glob("$tpl_folder*.tpl") as $filename){
						$split = explode("/",$filename);
						$tpl_name = $split[count($split) - 1];
						$found = true;
						break;
					}
					if(!$found){
						echo "No pages were found!";
						return;
					}
				}
				Viewer::display_result($tpl_name,false);
			}
		}
	}
}
?>