<?php
use BattlePHP\Core\Controller;

class ActionHome extends Controller{
	public function index(){
		$this->display_page('section.home.tpl');
	}
}