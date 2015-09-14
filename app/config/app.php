<?php

if (
	isset ($_SERVER['HTTPS']) ||
	(isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == "https")
) {
	$absluteUrl = 'https';
} else {
	$absoluteUrl = 'http';
}
$absoluteUrl .= '://' . $_SERVER['HTTP_HOST'] . '/';

return array(
	'absoluteurl' => $absoluteUrl,
	'url' => '/'
);