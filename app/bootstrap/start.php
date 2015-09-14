<?php
error_reporting (E_ALL);

$loader = require_once '../../vendor/autoload.php';

// Start the app
$app = \Neuron\Application::getInstance ();

// Set config folder
\Neuron\Config::folder (__DIR__ . '/../config/');

// Optionally, set an environment
$hostname = trim (file_get_contents ('/etc/hostname'));

switch ($hostname)
{
	case 'my-computer':
	case 'thijs-home-i7':
	case 'thijs-i7':
		\Neuron\Config::environment ('development');
		break;
}

// Set the template folder
\Neuron\Core\Template::addPath (__DIR__ . '/../templates/');

// Always set a locale
$app->setLocale ('nl_BE.utf8');

// Load the router
$app->setRouter (include ('router.php'));

// Return app
return $app;