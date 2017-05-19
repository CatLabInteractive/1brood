<?php

return array (

	'from' => 'support@catlab.be',

	'services' => array (
		'smtp' => array (
		    'server' => getenv("SMTP_SERVER"),
			'username' => getenv("SMTP_USERNAME"),
			'password' => getenv("SMTP_PASSWORD"),
            'security' => (getenv("SMTP_SECURITY") ? getenv("SMTP_SECURITY") : 'ssl'),
            'port' => (getenv("SMTP_PORT") ? getenv("SMTP_PORT") : 465)
		)
	)

);