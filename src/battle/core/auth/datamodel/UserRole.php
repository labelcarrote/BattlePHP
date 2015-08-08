<?php
namespace BattlePHP\Core\Auth;

class UserRole {
	
	public $id = 0;
	public $name = "";
	
	public function __construct($id,$name){
		$this->id = $id;
		$this->name = $name;
	}

	public static function get_roles(){
		return array(
			0 => new UserRole(0,"?"), 
			1 => new UserRole(1,"user"),
			2 => new UserRole(2,"admin")
		);
	}
	
	public function __toString(){
		return $this->name;
	}
}
