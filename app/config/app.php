<?php

if (isset ($_SERVER['HTTPS'])) {
	$absluteUrl = 'https';
} else {
	$absoluteUrl = 'http';
}
$absoluteUrl .= '//' . $_SERVER['HTTP_HOST'] . '/';

return array(
	'absoluteurl' => $absoluteUrl,
	'url' => '/'
);