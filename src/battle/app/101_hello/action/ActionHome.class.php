<?php
use BattlePHP\Core\Controller;
use BattlePHP\Core\Request;
/**
 * CLASS ActionHome (Controller)
 *
 * A basic example controller for 101_hello app.
 * 
 * Pages:
 * - index : /home or /
 * - sub_page : /home/sub_page
 *
 */
class ActionHome extends Controller{
	// [/home,/]
	public function index(){
		$this->assign('title',"101_hello");
		$this->display_view('section.index.tpl');
	}

	// [/home/sub_page]
	public function sub_page(){
		echo "This is a subpage, follow <a href='".Request::get_application_virtual_root()."'>this link</a> to go back home";
	}
}