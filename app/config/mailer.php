<?php
$username = getenv("MANDRILL_USERNAME");
$key = getenv("MANDRILL_KEY");

return array (

	'from' => 'info@1brood.be',

	'services' => array (
		'mandrill' => array (
			'username' => $username,
			'key' => $key
		)
	)

);