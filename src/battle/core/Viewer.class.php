<?php
require_once 'core/i18n/Localization.php';
require_once 'lib/smarty/Smarty.class.php';

/**
 * Viewer
 *
 */
class Viewer extends Smarty{
	
	// ---- Singleton ----

	private static $instance = null ;

	public static function getInstance(){
		if (!isset(self::$instance)) {
			$c = __CLASS__;
			self::$instance = new $c;
		}
		return self::$instance ;
	}

	public function __construct(){
		parent::__construct();
		$this->setConfigDir(Configuration::SMARTY_CONFIG_DIR);
		$this->setCacheDir(Configuration::SMARTY_CACHE_DIR);

		// register i18n substitute helper
		$this->registerFilter("output","i18n_substitute_text");

		// focus on the desired application
		$application = Request::isset_or($_SESSION["application"]);
		$this->setTemplateDir("app/$application/".Configuration::SMARTY_TEMPLATE_DIR);
		$this->setCompileDir(Configuration::SMARTY_COMPIL_DIR."/$application");
		
		if(!is_dir($this->compile_dir))
			mkdir($this->compile_dir);

		$this->assign_config_infos();
	}

	// ---- Private Helpers ----
	
	/**
	 * Assign some template helper values (base url and form base url)
	 * TODO : deal with single app (if monoapp -> current_app_url = root_url (+ htacess biz))
	 */
	private function assign_config_infos(){
		$this->assign(Configuration::ROOT_URL, Request::get_root_url());
		$this->assign(Configuration::CURRENT_APP_URL, Request::get_application_root());
		$this->assign(Configuration::CURRENT_APP_VIRTUAL_URL, Request::get_application_virtual_root());
		$this->assign(Configuration::FULL_URL,Request::get_full_url());
	}

	// ---- Public Methods ----

	/**
	 * Displays root index.tpl assigned with the list of all the applications found in "app" folder
	 */
	public static function display_root(){
		if(!is_file("app/index.tpl"))
			echo "no page found on root...";
		else{
			$content = "";
			foreach (glob("app/*",GLOB_ONLYDIR) as $dirname) {
				$split = explode("/",$dirname);
				$shortdir = $split[count($split) - 1];
				$shortdirDisplay = ucfirst($shortdir);
				$content .= "- <a href='$shortdir'>$shortdirDisplay</a> (<a href='https://github.com/labelcarrote/Battle.PHP' alt='source on (GI)TEUB'>source</a>)<br/>";
			}
			$viewManager = self::getInstance();
			$viewManager->assign('apps',$content);
			$viewManager->display_view("app/index.tpl","/");
		}
	}

	public static function display_result($tpl,$as_page){
		$view_manager = new self();
		if($as_page == true)
			$view_manager->display_page($tpl);
		else
			$view_manager->display_view($tpl);
	}

	/**
	 * Displays given page (look for a "page.main.tpl" if no pagetemplate given)
	 */
	public function display_page($content,$pagetemplate = NULL){
		// Assign current section name
		$this->assign('controller', ucfirst($_SESSION['controller']));
		$this->assign('content',$content);
		if(!is_null($pagetemplate))
			$this->display($pagetemplate);	
		else
			$this->display('page.main.tpl');
	}

	/**
	 * Displays given view/template
	 */
	public function display_view($view,$template_dir = NULL){
		if(!is_null($template_dir))
			$this->template_dir = $template_dir;
		$this->display($view);
	}
	
	/**
	 * Fetchs given view/template and returns it
	 */
	public function fetch_view($view,$template_dir = NULL){
		if(!is_null($template_dir))
			$this->template_dir = $template_dir;
		return $this->fetch($view);
	}
}
?>
