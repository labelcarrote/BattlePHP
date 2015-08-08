<?php
namespace BattlePHP\Core\Auth;
use BattlePHP\Model\Entity;
/**
 * User
 */
class User extends Entity{

	public function __construct($userdb = null){
		$this->fields = array(
			'id' => 0,
			'role_id' => 0,
			'role_name' => '',
			'application' => '',
			'mail' => '',
			'login' => '',
			'hashed_password' => '',
			'date_last_password_update' => '',
			'date_creation' => '',
			'confirmation_token' => '',
			'has_confirmed' => false,
			'last_ip' => '',
			'date_last_connection' => '',
			'marked_for_deletion' => false,
			'marked_for_deletion_date' => '',
		);

		if(is_array($userdb)){
			foreach($userdb as $field => $value)
				$this->$field = $value; //note : $this->$field ! with a $ before the field!
		}
		
		$this->role_name = UserRole::get_roles()[$this->role_id];
	}
	
	public static function create_user_from_db($userdb){
		return new User($userdb);
	}
	
	public static function create_users_from_db($usersdb){
		$users = array();
		foreach($usersdb as $userdb)
			$users[] = self::create_user_from_db($userdb);
		return $users;
	}
}
