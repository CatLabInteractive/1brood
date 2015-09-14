<?php
$url = getenv("CLEARDB_DATABASE_URL");

if (!$url)
	return array ();

$url = parse_url($url);
return array (
	'mysql' => array (
		'host' => $url["host"],
		'username' => $url["user"],
		'password' => $url["pass"],
		'database' => substr($url["path"], 1),
		'charset' => 'utf8',
		'prefix' => 'neuron_'
	)
);