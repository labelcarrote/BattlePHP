<?php
namespace BattlePHP\Core\Auth;
use BattlePHP\Core\Request;
/**
 * UserManager
 */
class UserManager{
	// -----------------
	// ---- QUERIES ----
	// -----------------

	public static function count_all_users($application = null){
		if($application === null)
			$application = Request::get_current_application();

		return UserDB::getInstance()->count_all_users($application);
	}

	public static function exists_user_from_mail_or_login($login_or_mail,$application = null){
		if($application === null)
			$application = Request::get_current_application();

		return UserDB::getInstance()->exists_user_from_mail($login_or_mail,$application) 
			|| UserDB::getInstance()->exists_user_from_login($login_or_mail,$application);
	}

	public static function get_user($user_id,$application = null){
		if($application === null)
			$application = Request::get_current_application();

		$userdb = UserDB::getInstance()->get_user($user_id,$application);
		if($userdb === null)
			return null;

		return User::create_user_from_db($userdb);
	}

	public static function get_user_from_confirmation_token($confirmation_token,$application = null){
		if($application === null)
			$application = Request::get_current_application();

		$userdb = UserDB::getInstance()->get_user_from_confirmation_token($confirmation_token,$application);
		if($userdb === null)
			return null;

		return User::create_user_from_db($userdb);
	}

	public static function get_user_from_login($login,$application = null){
		if($application === null)
			$application = Request::get_current_application();

		$userdb = UserDB::getInstance()->get_user_from_login($login,$application);
		if($userdb === null)
			return null;

		return User::create_user_from_db($userdb);
	}

	public static function get_user_from_mail($mail,$application = null){
		if($application === null)
			$application = Request::get_current_application();

		$userdb = UserDB::getInstance()->get_user_from_mail($mail,$application);
		if($userdb === null)
			return null;

		return User::create_user_from_db($userdb);
	}

	public static function get_users($page_id, $nb_user_by_page,$application = null){
		if($application === null)
			$application = Request::get_current_application();

		return User::create_users_from_db(UserDB::getInstance()->get_users($page_id, $nb_user_by_page,$application));
	}

	// -------------------------------------------
	// ---- COMMANDS : Create, Update, Delete ----
	// -------------------------------------------

	public static function update_user_last_connection($user_id, $user_ip){
		return UserDB::getInstance()->update_user_last_connection($user_id,$user_ip);
	}

	public static function regenerate_user_confirmation_token($user){
		return UserDB::getInstance()->regenerate_user_confirmation_token($user);
	}

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
