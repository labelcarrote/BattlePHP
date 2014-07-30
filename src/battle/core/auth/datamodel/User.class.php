<?php
require_once 'core/model/ValueObject.class.php';

/**
 * User
 */
class User extends ValueObject{

	public function __construct($userdb = null){
		$this->fields = array(
			'id' => 0,
			'role_id' => 0,
			'role_name' => '',
			'application' => '',
			'mail' => '',
			'login' => '',
			'hashed_password' => '',
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
	}

	public function __toString(){
		return "user [ $id $role_id $role_name $application $mail $login $hashed_password $has_confirmed $date_creation $last_ip $mark_for_deletion $mark_for_deletion_date $confirmation_token ]";
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

	// WIP
	// TODO : from which post ? any form submit concerning a user? which one ? 
	public static function create_user_from_post(){
		$user = new User();
		$user->id = Request::isset_or($_POST['id'],0);
		$user->role_id = Request::isset_or($_POST['role_id'], 1);
		$user->mail = Request::isset_or($_POST['mail'], "");
		$user->application = Request::isset_or($_POST['application'], "flipapart");
		$user->login = Request::isset_or($_POST['login'], "");
		$user->hashed_password = Request::isset_or($_POST['hashed_password'], "");
		$user->has_confirmed = Request::isset_or($_POST['has_confirmed'], false);
		$user->date_creation = Request::isset_or($_POST['date_creation'], "");
		$user->last_ip = Request::isset_or($_POST['last_ip'], "");
		$user->marked_for_deletion = Request::isset_or($_POST['marked_for_deletion'], false); 
		$user->marked_for_deletion_date = Request::isset_or($_POST['marked_for_deletion_date'], "");
		$user->confirmation_token = Request::isset_or($_POST['confirmation_token'], "");
		return $user;
	}
}
?>