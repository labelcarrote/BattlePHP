<?php
/* index.php */

// charset for HTTP requests
ini_set('default_charset', 'UTF-8');

// set separator to &amp; instead of & (W3C)
ini_set('arg_separator.output','&amp;');

// set include path
$include_path = array(
	'core/',
	'lib/smarty/sysplugins/',
	'lib/parsedown-JonPotiron/'
);
ini_set('include_path',ini_get('include_path').PATH_SEPARATOR.implode(PATH_SEPARATOR,$include_path));

// only use cookies (no url based sessions, no ?PHPSESSID)
ini_set('session.use_only_cookies', true);

// initialize session
session_cache_limiter ('private, must-revalidate');
session_start();

// load configuration
require_once('config/config.php');

// include core in autoload
function autoloadcore($classname) {
	if((@include_once $classname.'.class.php') === false) {
		// try other naming convention
		if((@include_once $classname.'.php') === false) {
			// and try again for fu***g Smarty sh**y naming convention
			include_once strtolower($classname).'.php';
		}
	}
}
    
spl_autoload_register('autoloadcore');

// load router
Router::run();
?>
