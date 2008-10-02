<?php

include ('php/connect.php');

$page = isset ($_GET['page']) ? $_GET['page'] : 'home';

// Fetch the right page
$page = Pages_Page::getPage ($page);
echo $page->getHTML ();

?>