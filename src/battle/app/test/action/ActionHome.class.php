<?php
use BattlePHP\Core\Controller;

class ActionHome extends Controller{
	public function index(){
		$this->display_view('index.tpl');
	}  
	// This method ! Add it.
	public function hello(){
		$person = "georges";
		$this->assign("famous_people",$person);
		$this->display_view('section.hello.tpl');
	}
}