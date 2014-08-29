<?php
require_once 'core/auth/db/UserDB.class.php';
/**
 * UserManager
 */
class UserManager{
	// -----------------
	// ---- QUERIES ----
	// -----------------

	public static function get_user($user_id){
		return User::create_user_from_db(UserDB::getInstance()->get_user($user_id));
	}

	public static function get_user_from_confirmation_token($confirmation_token,$application = null){
		if($application === null)
			$application = Request::get_application();

		return User::create_user_from_db(UserDB::getInstance()->get_user_from_confirmation_token($confirmation_token,$application));
	}

	public static function get_user_from_login($login,$application = null){
		if($application === null)
			$application = Request::get_application();

		return User::create_user_from_db(UserDB::getInstance()->get_user_from_login($login,$application));
	}

	// -------------------------------------------
	// ---- COMMANDS : Create, Update, Delete ----
	// -------------------------------------------

	public static function save($user){
		if($user === null)
			return false;

		return UserDB::getInstance()->upsert_user($user);
	}

	public static function delete($user_id){
		if(!isset($user_id) || $user_id < 1)
			return false;

		return UserDB::getInstance()->delete($user_id);
	}
}
?>