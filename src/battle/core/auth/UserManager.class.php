<?php
require_once 'core/auth/db/UserDB.class.php';
/**
 * UserManager
 */
class UserManager{

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