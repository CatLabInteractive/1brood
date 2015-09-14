<?php

header('Content-Type: text/html; charset=utf-8');

$app = include ('../bootstrap/start.php');
$app->dispatch ();