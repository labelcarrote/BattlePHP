<?php
use BattlePHP\Core\Request;
/**********************************************************************
* NavigationHelper
*
* @author jonpotiron, touchypunchy
*
***********************************************************************/
class NavigationHelper{
	
	const MAX_ITEM = 5;
	const BREADCRUMBS_SESSION_VAR = 'NavigationHelper';
	const DEFAULT_NAME = 'breadcrumbs';

	/* 
	 * Adds an item to a breadcrumbs
	 * and returns breadcrumbs
	 *
	 * @uses self::get()
	 * @param string $item_name
	 * @param string $breadcrumbs_name
	 * @return array 
	 *
	 */
	public static function add_item($item_name, $breadcrumbs_name = self::DEFAULT_NAME){
		self::set_in_session($breadcrumbs_name);
		
		$current_url = Request::get_full_url().$_SERVER['REQUEST_URI'];
		$current_url = rtrim($current_url,'/');//."/";

		// remove if already there
		if(isset($_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name]['items'][$item_name]) 
			&& $_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name]['items'][$item_name]['url'] == $current_url){
			unset($_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name]['items'][$item_name]);
		}
		if($current_url != Request::get_full_url().Request::get_application_virtual_root()){
			$_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name]['items'][$item_name] = ['url' => $current_url, 'name' => $item_name];
			$_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name]['position'] = array_search($item_name, array_keys($_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name]['items'])) +1;
		} else {
			$_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name]['position'] = 0;
		}

		if(count($_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name]['items']) > self::MAX_ITEM){
			array_shift($_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name]['items']);
			$_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name]['position'] = 5;
		}
		
		return self::get($breadcrumbs_name);
	}
	
	/* 
	 * Gets a breadcrumbs
	 *
	 * @param string $breadcrumbs_name
	 * @return array 
	 *
	 */
	public static function get($breadcrumbs_name = self::DEFAULT_NAME){
		self::set_in_session($breadcrumbs_name);
		return $_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name];
	}
	
	/* 
	 * Checks breadcrumbs var existence in $_SESSION
	 * and creates it if necessary
	 *
	 * @param string $breadcrumbs_name
	 * @return void 
	 *
	 */
	private static function set_in_session($breadcrumbs_name = self::DEFAULT_NAME){
		$_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name] = 
			!isset($_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name])
			? ['position' => 0, 'items' => []]
			: $_SESSION[self::BREADCRUMBS_SESSION_VAR][$breadcrumbs_name];
	}
}
