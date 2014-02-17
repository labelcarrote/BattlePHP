<?php
require_once 'core/auth/AuthHelper.class.php';
require_once 'lib/phpass-0.3/PasswordHash.php';
//require_once 'core/auth/FacebookService.class.php';

/**
 * AuthService
 * Responsible of the user authentication management.
 * Note : 
 * - requires an mysql db containing the user & role tables
 * - currently, all data validation happens before calling any of these methods (in the 'validate()'' methods of form models), 
 * so there is NO DATA VALIDATION HERE
 */
class AuthService{
	
	public static function login($login,$password){
		$identity = new Identity();
		$identity->login = $login;
		$identity->password = $password;
		return AuthHelper::authenticate(AuthHelper::AuthTypeUser,$identity);
	}
	
	public static function logout(){
		AuthHelper::unauthenticate();
	}
	
	public static function register($mail,$login,$password,$application){
		// Initialize the hasher without portable hashes (this is more secure)
		$hasher = new PasswordHash(8, false);

		$user = new User();
		$user->mail = $mail;
		$user->login = $login;
		$user->hashed_password = $hasher->HashPassword($password);//crypt($password,Configuration::SUPA_SALT);
		$user->confirmation_token = sha1(uniqid($user->login, true));
		$user->role_id = AuthHelper::role_name_to_id("user");
		$user->last_ip = AuthHelper::get_user_ip();
		$user->application = $application;

		// send confirmation mail ? Currently in ActionAuth...
		return UserDB::getInstance()->add($user);
	}
	
	// TODO
	public static function confirmation($userid, $confirmationtoken){
		//confirm registration of user (from confirmation mail)
		// getuser from id
		// compare confirmation tokens
		// if(same) userdb->has_confirmed = true/1;
	}
	
	// TODO
	public static function change_password($old,$new){
		// update password
		// send mail?
	}
	
	// TODO
	public static function unregister(){
		// update user state to unregistered
	}
	
	// ---- FACEBOOK ----

	/**
	 * Note : to check if user is authenticated on facebook :
	 * isset(get_user_profile($fbm))
	 */
	public static function get_user_profile($facebook_manager){
		return $facebook_manager->get_user_profile();
	}	

	public static function get_loginout_url($facebook_manager,$user_profile){
		return $facebook_manager->get_url_loginout($user_profile);
	}
	
	// ---- Helpers ----
	
	public static function get_identity_from_post(){
		$identity = new Identity();
		$identity->login = Request::isset_or($_POST['login'],"");
		$identity->mail = Request::isset_or($_POST['mail'],"");
		$identity->password = Request::isset_or($_POST['password'],"");
		return $identity;
	}
}
?>