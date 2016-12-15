<?php
use BattlePHP\Core\Request;
/********************************************************************
* CLASS ActionLol (Controller)
*
* Another basic example controller for 101_hello app.
* 
* Pages:
* - index : /lol
* - wat : /lol/wat
*
*********************************************************************/
class ActionLol extends BattlePHP\Core\Controller{
	
	// [/lol]
	public function index(){
		echo "route : /lol, follow <a href='".Request::get_application_virtual_root()."'>this link</a> to go back home";
	}

	// [/lol/wat]
	public function wat(){
		echo "route : /lol/wat, follow <a href='".Request::get_application_virtual_root()."'>this link</a> to go back home";
	}
}