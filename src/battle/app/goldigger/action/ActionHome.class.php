<?php
use BattlePHP\Core\Controller;
/**
 * CLASS ActionHome (Controller)
 *
 * An example controller for goldigger app.
 * 
 * Pages:
 * - index : /home or /
 *
 */
class ActionHome extends Controller{

	// [/home,/]
	public function index(){
		/*$goldrate : array ["rate" => 1024, "last-update" : ...];*/

		$this->display_view(
			'section.index.tpl',
			[
				'title' => "Goldigger"
			]
		);
	}
}
