<?php
/* index.php */

// charset for HTTP requests
ini_set('default_charset', 'UTF-8');

// set separator to &amp; instead of & (W3C)
ini_set('arg_separator.output','&amp;' );

// set include path
ini_set('include_path', './');

// only use cookies (no url based sessions, no ?PHPSESSID)
ini_set('session.use_only_cookies', true);

// initialize session
session_cache_limiter ('private, must-revalidate');
session_start();

// load configuration
require_once('config/config.php');

// include core in autoload
function autoloadcore($classname) {
    //fix for smarty 3 shitty autoloader
    if (substr($classname, 0, 15) == "Smarty_Internal") {
        $classname = "lib/smarty/sysplugins/" . strtolower($classname);
        require_once $classname . ".php";
    }
    else{
        include "core/$classname.class.php";
    }
}
    
spl_autoload_register('autoloadcore');

// load router
Router::run();
?>
