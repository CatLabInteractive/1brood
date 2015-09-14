<?php

// Set session ID if provided
if (isset ($_GET['phpSessionId']) && !empty ($_GET['phpSessionId'])) {
	session_id ($_GET['phpSessionId']);
}

session_start();
include ('config.php');
date_default_timezone_set (TIME_ZONE);

function customMail ($target, $subject, $msg)
{
	die ('No mailer defined');
}