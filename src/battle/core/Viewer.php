<?php
namespace BattlePHP\Core;
use \Configuration;
/**
 * Viewer
 */
class Viewer extends \Smarty{
	
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
		$this->registerFilter("output",'BattlePHP\Core\i18n_substitute_text');

		// focus on the desired application
		$application = Request::isset_or($_SESSION["application"]);

		$this->setTemplateDir("app/$application/".Configuration::SMARTY_TEMPLATE_DIR);
		$this->setCompileDir(Configuration::SMARTY_COMPIL_DIR."/$application");

		if(!is_dir($this->getCompileDir()))
			mkdir($this->getCompileDir());

		$this->assign_config_infos();

		// remove compile warning
		//$this->error_reporting = E_ALL;
       	//$this->muteExpectedErrors();
		//$this->debugging = true;
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
		$this->assign(Configuration::IS_MOBILE_DEVICE,Request::is_mobile_device());
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
			foreach (Router::get_all_apps() as $app_name){
				$shortdirDisplay = ucfirst($app_name);
				$content .= "- <a href='$app_name'>$shortdirDisplay</a> (<a href='https://github.com/labelcarrote/Battle.PHP' alt='source on (GI)TEUB'>source</a>)<br/>";
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
			$this->setTemplateDir($template_dir);
		$this->display($view);
	}
	
	/**
	 * Fetchs given view/template and returns it
	 */
	public function fetch_view($view,$template_dir = NULL){
		if(!is_null($template_dir))
			$this->setTemplateDir($template_dir);
		return $this->fetch($view);
	}
}
