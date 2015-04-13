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
function autoloadcore($class_name) {
	// set namespace as dir path
	$class_path = str_replace('\\','/',$class_name);
	// must try X naming convention due to shi**y lib implementation 
	if(stream_resolve_include_path($class_path.'.class.php')){
		require_once $class_path.'.class.php';
	} elseif(stream_resolve_include_path($class_path.'.php')){
		require_once $class_path.'.php';
	} elseif(stream_resolve_include_path('class.'.strtolower($class_path).'.php')){
		require_once 'class.'.strtolower($class_path).'.php';
	} elseif(stream_resolve_include_path(strtolower($class_path).'.php')){
		require_once strtolower($class_path).'.php';
	}
}
    
spl_autoload_register('autoloadcore');

// load router
Router::run();
?>
