<?php
/*
 * Main configuration table $GLOBAL_CONF
 *
 * Notes :
 * - path are relative to the project root web folder (the php entry point, index.php)
 */
class Configuration{

	// general information
	const PROJECT_NAME = "battlephpv1";//?
	const PROJECT_VERSION = "0.2";
	const PROJECT_CONTACT = "labelcarrote@gmail.com";

	// debug / trace settings
	const PRODUCTION_MODE = true;
	const MAIN_LOG_FILE = "main.log";

	// smarty configuration
	const SMARTY_TEMPLATE_DIR = "/view";
	const SMARTY_COMPIL_DIR = "tmp/tpl_comp";
	const SMARTY_CONFIG_DIR = "config/smarty";
	const SMARTY_CACHE_DIR = "tmp/tpl_cache";

	// smarty template helpers' name
	const ROOT_URL = "root_url";
	const CURRENT_APP_VIRTUAL_URL = "current_app_virtual_url";
	const CURRENT_APP_URL = "current_app_url";

	// database
	const DB_HOST = "127.0.0.1";    // host : "mysql5-1" ;
	const DB_USER = "root";         // user name
	const DB_PASS = "";             // password
	const DB_NAME = "sandboxstorm"; // database name

	const SIMPLE_AUTH_PASS = '$1$bvN4JjDF$yUZPqWjmpdJgxV1UojMog/';

	// mail 
	const MAIL_HOST = "smtp..com"; // SMTP server
	const MAIL_PORT = 587; // set the SMTP port
	const MAIL_USERNAME = "pm@s.com"; // mail / username
	const MAIL_PASS = ""; // password
	const MAIL_SMTPDEBUG  = false; //      

	// FB
	const FB_APP_ID = "";
	const FB_SECRET = "";
	const FB_CALLBACK_LOGIN_URL = ".../fblogout";
	const FB_CALLBACK_LOGOUT_URL = ".../fbcallback";
}
?>