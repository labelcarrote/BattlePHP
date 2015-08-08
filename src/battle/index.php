<?php
// index.php

// charset for HTTP requests
ini_set('default_charset', 'UTF-8');

// set separator to &amp; instead of & (W3C)
ini_set('arg_separator.output','&amp;');

// only use cookies (no url based sessions, no ?PHPSESSID)
ini_set('session.use_only_cookies', true);

// initialize session
session_cache_limiter ('private, must-revalidate');
session_start();

// load configuration
require_once 'config/config.php';

// load composer autoload
require_once 'vendor/autoload.php';

// load router
\BattlePHP\Core\Router::run();
