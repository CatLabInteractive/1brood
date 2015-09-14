<?php

/*
	Configuration	
*/

define ('APP_VERSION', 0.2);

define ('BASE_PATH', dirname (dirname (__FILE__)));

define ('ABSOLUTE_URL', \Neuron\Config::get('app.url'));
define ('RELATIVE_URL', \Neuron\Config::get('app.url'));

define ('TIME_ZONE', 'Europe/Brussels');
define ('BASE_URL', '');

define ('DATETIME', 'd.m.Y H:i');

define ('DEFAULT_TEMPLATE_DIR', BASE_PATH . '/templates/default');

define ('STATIC_URL', '');

define ('IMAGE_URL', ABSOLUTE_URL.'images/');
define ('LANGUAGE_DIR', BASE_PATH . '/language/');
define ('TEMPLATES_DIR', 'templates/');

// Google stuff
define ('GOOGLE_ANALYTICS', 'UA-459768-12');

define ('DB_SERVER', \Neuron\Config::get('database.mysql.host'));
define ('DB_USERNAME', \Neuron\Config::get('database.mysql.username'));
define ('DB_PASSWORD', \Neuron\Config::get('database.mysql.password'));
define ('DB_DATABASE', \Neuron\Config::get('database.mysql.database'));

if (isset ($_GET['language'])) {
	if (file_exists (LANGUAGE_DIR . $_GET['language']) && is_dir (LANGUAGE_DIR . $_GET['language'])) {
		setCookie ('language', $_GET['language'], time () + 60*60*24*365, '/');
		$_COOKIE['language'] = $_GET['language'];
	}
}

if (isset ($_GET['layout'])) {
	if (
		file_exists (TEMPLATES_DIR . $_GET['layout'])
		&& is_dir (TEMPLATES_DIR . $_GET['layout'])
	) {
		setCookie ('layout', $_GET['layout'], time () + 60*60*24*365, '/');
		$_COOKIE['layout'] = $_GET['layout'];
	}
}

// Get right language tag
if (isset ($_COOKIE['language'])) {
	define ('LANGUAGE_TAG', $_COOKIE['language']);
} else {
	define ('LANGUAGE_TAG', 'nl');
}

// Get right layout
if (isset ($_COOKIE['layout'])) {
	define ('TEMPLATE_DIR', TEMPLATES_DIR . $_COOKIE['layout']);
} else {
	define ('TEMPLATE_DIR', TEMPLATES_DIR . 'buttons');
}

function getLanguages ()
{
	$o = array ();

	$o[] = array ('nederlands', 'nl');
	//$o[] = array ('waxiaans', 'nl-wax');
	//$o[] = array ('west-vlaams', 'nl-wv');

	return $o;
}

function getLayouts ()
{
	$o = array ();

	$o[] = array ('buttons', 'buttons');
	//$o[] = array ('default', 'default');
	//$o[] = array ('matrix', 'matrix');

	return $o;
}