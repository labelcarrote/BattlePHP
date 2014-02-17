<?php
require_once 'lib/facebook/facebook-php-sdk-master/src/facebook.php';
/**
 * FacebookManager
 * - manages facebook user auth/connect 
 * - uses OpenGraph api
 * - works with the javascript sdk (client side)
 *
 * Configuration is set in config.php 
 * - FB_APP_ID
 * - FB_SECRET
 * - FB_CALLBACK_LOGIN_URL
 * - FB_CALLBACK_LOGOUT_URL
 */
class FacebookService{

	private $facebook;

	public function __construct(){
		$this->facebook = new Facebook(array(
		  'appId'  => Configuration::FB_APP_ID,
		  'secret' => Configuration::FB_SECRET //,'cookie' => true
		));
	}

	public function get_user_profile(){
		// Get User ID
		$user = $this->facebook->getUser();
		$user_profile = null;
		
		// We may or may not have this data based on whether the user is logged in.
		// If we have a $user id here, it means we know the user is logged into
		// Facebook, but we don't know if the access token is valid. An access
		// token is invalid if the user logged out of Facebook.

		if ($user) {
		  try {
		    // Proceed knowing you have a logged in user who's authenticated.
		    $user_profile = $this->facebook->api('/me');
		  } catch (FacebookApiException $e) {
		    error_log($e);
		    //echo $e;
		  }
		}
		return $user_profile;
	}

	public function get_url_loginout($user_profile){
		// Login or logout url will be needed depending on current user state.
		return ($user_profile)
			? $this->facebook->getLogoutUrl(array('next' => Configuration::FB_CALLBACK_LOGIN_URL))
			: $this->facebook->getLoginUrl(array('redirect_uri' => Configuration::FB_CALLBACK_LOGOUT_URL));
	}
}
?>