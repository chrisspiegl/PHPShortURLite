<?php
define('SHORT_URL', 'http://s.cspiegl.com');	// Your shortener URL (no trailing '/')
define('DEFAULT_URL', 'http://cspiegl.com');	// Your default redirect site (no trailing '/')

define('AUTH', true);				// true if authentication is needed, false if everybody can shorten.
define('CACHE', true);				// Cache the original link in a file to prevent sql requests
define('TRACK', true);				// Track clicks / ip / time / user_agent / refferal
define('BOOKMARK_CREATOR', false);	// Set to false to deactivate the bookmark generator (this is saver if you have a easy to guess username)
define('DEBUG', false);				// Activate for some messages and testing

define('CHECK_404_URL', true);	// Checks if the URL returns 404 and does not create shortlink if so
define('CUSTOMTAG', true);
define('ALLOWED_CHARS', '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ');

// Your user authentication. Add new users via adding an element to the array and generating a new AUTH_KEY
$API_AUTH = array('cspiegl'=>'27459b72456f8fd37e7080bb7fcbe6884c54697f');

// Define some folders for the process
define('DOC_ROOT', dirname(__FILE__));
define('DB_FILE_NAME', DOC_ROOT . '/data/db.sqlite');
define('CACHE_DIR', DOC_ROOT . '/data/cache');	// (no trailing '/')
define('TRACK_DIR', DOC_ROOT . '/data/log');	// (no trailing '/')
define('TRACK_FILE_NAME', date("Y_m") . '_access.log');	// Creates one logfile for each month of the year

// Stop editing here!
require_once(DOC_ROOT . '/functions.php');
require_once(DOC_ROOT . '/Shortener.php');