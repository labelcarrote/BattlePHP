<?php
use BattlePHP\Core\Controller;
require_once __DIR__.'/../model/GoldRater.php';
/********************************************************************
* CLASS ActionHome (Controller)
* 
* Pages:
* - index : /home or /
*
*********************************************************************/
class ActionHome extends Controller{

	// [/home,/]
	public function index(){
		$this->display_view(
			'section.index.tpl',
			[
				'title' => "Goldigger",
				'rate' => GoldRater::get_rate()
			]
		);
	}
}
