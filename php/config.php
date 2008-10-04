<?php

/*
	Configuration	
*/

define ('APP_VERSION', 0.2);

if ($_SERVER['SERVER_NAME'] == 'daedelserv.local' || $_SERVER['SERVER_NAME'] == 'thijsvanderschaeghe.no-ip.com')
{
	define ('DB_USERNAME', 'myuser');
	define ('DB_PASSWORD', 'myuser');
	define ('DB_SERVER', 'localhost');
	define ('DB_DATABASE', '1brood');
	
	define ('ABSOLUTE_URL', 'http://'.$_SERVER['SERVER_NAME'].'/1brood/');
}
else
{
	define ('DB_USERNAME', 'b6557bro_brood');
	define ('DB_PASSWORD', 'Slwopza45b');
	define ('DB_SERVER', 'localhost');
	define ('DB_DATABASE', 'b6557bro_brood');
	
	define ('ABSOLUTE_URL', 'http://'.$_SERVER['SERVER_NAME'].'/');
}

define ('TIME_ZONE', 'Europe/Brussels');
define ('BASE_URL', '');

define ('DATETIME', 'd.m.Y H:i');

define ('DEFAULT_TEMPLATE_DIR', 'templates/default');

define ('STATIC_URL', '');

define ('IMAGE_URL', 'images/');
define ('LANGUAGE_DIR', 'language/'); 
define ('TEMPLATES_DIR', 'templates/');

// Mail settings
define ('MAILER_MAILER', 'smtp');

define ('MAILER_HOST', 'localhost');
define ('MAILER_PORT', 25);

define ('MAILER_USER', 'noreply+1brood.be');
define ('MAILER_PASS', 'Slwopza45b');

define ('MAILER_FROM', 'noreply@1brood.be');


// Google stuff
define ('GOOGLE_ANALYTICS', 'UA-459768-12');

if (isset ($_GET['language']))
{
	if
	(
		file_exists (LANGUAGE_DIR . $_GET['language'])
		&& is_dir (LANGUAGE_DIR . $_GET['language'])
	)
	{
		setCookie ('language', $_GET['language'], time () + 60*60*24*365);
		$_COOKIE['language'] = $_GET['language'];
	}
}

if (isset ($_GET['layout']))
{
	if
	(
		file_exists (TEMPLATES_DIR . $_GET['layout'])
		&& is_dir (TEMPLATES_DIR . $_GET['layout'])
	)
	{
		setCookie ('layout', $_GET['layout'], time () + 60*60*24*365);
		$_COOKIE['layout'] = $_GET['layout'];
	}
}

// Get right language tag
if (isset ($_COOKIE['language']))
{
	define ('LANGUAGE_TAG', $_COOKIE['language']);
}

else
{
	define ('LANGUAGE_TAG', 'nl');
}

// Get right layout
if (isset ($_COOKIE['layout']))
{
	define ('TEMPLATE_DIR', TEMPLATES_DIR . $_COOKIE['layout']);
}
else
{
	define ('TEMPLATE_DIR', TEMPLATES_DIR . 'default');
}

function getLanguages ()
{
	$o = array ();

	$o[] = array ('nederlands', 'nl');
	$o[] = array ('waxiaans', 'nl-wax');
	//$o[] = array ('west-vlaams', 'nl-wv');

	return $o;
}

function getLayouts ()
{
	$o = array ();

	$o[] = array ('default', 'default');
	$o[] = array ('matrix', 'matrix');

	return $o;
}

?>
