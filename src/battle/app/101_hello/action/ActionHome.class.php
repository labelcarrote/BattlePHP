<?php
use BattlePHP\Core\Controller;
use BattlePHP\Core\Request;
/********************************************************************
* CLASS ActionHome (Controller)
*
* A basic example controller for 101_hello app.
* 
* Pages:
* - index : /home or /
* - sub_page : /home/sub_page
*
*********************************************************************/
class ActionHome extends Controller{
	
	// [/home,/]
	public function index(){
		$this->assign('title',"101_hello | home");
		$this->display_view('section.index.tpl');
	}

	// [/home/page1]
	public function page1(){
		$this->display_view('section.page1.tpl',[
			'title' => "101_hello | page1"
		]);
	}
}