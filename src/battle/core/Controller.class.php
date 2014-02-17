<?php
require_once 'core/auth/AuthHelper.class.php';
require_once 'core/auth/AuthService.class.php';
require_once 'core/i18n/Localization.php';

/**
 * Controller
 *
 * Core action controller
 * - go to action
 * - assign values (and errors) to view
 * - display view
 *
 * @author touchypunchy
 *
 */
class Controller{
	
	// view manager
	protected $view_manager;
	// page template
	protected $page_template;
	// content template
	protected $content_template;
	
	// errors
	private $errors = array();
	// error page
	private $page_error = "element.404.tpl";

	// ---- Constructors ----

	public function __construct($errors){
		i18n_init();
		if($errors !== NULL)
			$this->errors = $errors;
		$this->view_manager = Viewer::getInstance();
	}

	// ---- Methods ----

	/**
	 * Go to specified action of given section (and RUN IT!)
	 */
	protected function go_to_action($controller,$action,$application){
		Router::go_to_action($controller,$action,$application,$this->errors);
	}

	/**
	 * Assign value to view/template variable
	 */
	protected function assign($var,$value){
		$this->view_manager->assign($var,$value);
	}

	// --------------------------
	// ---- ERROR MANAGEMENT ----
	// --------------------------

	/**
	 * Add error to current errors list
	 */
	protected function add_error($error){
		$this->errors[] = $error;
	}
	
	private function assign_errors(){
		$this->assign('errors', $this->errors);
	}
	
	// -------------------------
	// ---- AUTH MANAGEMENT ----
	// -------------------------

	// TODO : move to authcontroller (executed after requested action)
	// Checks if user is authentified and assign authentication template-variables
	private function assign_auth_values(){
		$is_authenticated = AuthHelper::is_authenticated();
		$this->assign('logged', $is_authenticated);
		if($is_authenticated){
			// check if logged in local server
			$this->assign('admin', AuthHelper::is_current_user_admin());
			$this->assign('userprofile', AuthHelper::get_user_infos());

		}else{
			// check if logged in on FB
			/*$facebook_manager = new FacebookService();
			$userprofile = AuthService::get_user_profile($facebook_manager);
			$this->assign('userprofile', $userprofile);
			$this->assign('fbloginouturl', AuthService::get_loginout_url($facebook_manager,$userprofile));*/
		}
	}
	
	// --------------------------
	// ---- Extension methods ----
	// --------------------------

	protected function assign_custom_values(){}	

	// --------------------------------------------------------
	// ---- DISPLAY VIEW / PAGE / ERROR PAGE (HTTP STATUS) ----
	// --------------------------------------------------------

	/**
	 * Displays given page
	 */
	protected function display_page($content_template = NULL, $page_template = NULL){
		if(!is_null($content_template))
			$this->content_template = $content_template;
		if(!is_null($page_template))
			$this->page_template = $page_template;
		$this->assign_errors();
		$this->assign_auth_values();//TODO Move To authcontroller
		$this->assign_custom_values();
		$this->view_manager->display_page($this->content_template,$this->page_template);
	}

	/**
	 * Displays given view/template (as html/xml content, and not as a whole html page).
	 * Useful in ajax scenario !
	 */
	protected function display_view($view = NULL){
		if(!is_null($view))
			$this->content_template = $view;
		$this->assign_errors();
		$this->assign_auth_values();//TODO Move To authcontroller
		$this->assign_custom_values();
		$this->view_manager->display_view($this->content_template);
	}
	
	/**
	 * Displays an error page with a message
	 */
	protected function display_page_error($message){
		$this->assign('message',$message);
		$this->view_manager->display_page($this->page_error);
	}

	/**
	 * Fetch given view/template (as html/xml content, and not as a whole html page).
	 * Useful in ajax scenario !
	 */
	protected function fetch_view($view,$template_dir = NULL){
		return $this->view_manager->fetch($view, $template_dir);
	}
}
?>
