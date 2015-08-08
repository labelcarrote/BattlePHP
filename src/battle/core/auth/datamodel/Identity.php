<?php
namespace BattlePHP\Core\Auth;
/**
 * Identity
 *
 */
class Identity{
	
	public $login;
	public $password;
	public $application;
	
	public function __toString(){
		return "$login $application";
	}
}
