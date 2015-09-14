<?php

include ('php/connect.php');

$page = isset ($_GET['page']) ? $_GET['page'] : 'home';

$page = explode ('/', $page);
$page = array_shift ($page);

// Fetch the right page
$page = Pages_Page::getPage ($page);
echo $page->getHTML ();