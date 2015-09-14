<?php

/*

	CONNECT.PHP
	This file is loaded on every request.

*/


// Set session ID if provided
if (isset ($_GET['phpSessionId']) && !empty ($_GET['phpSessionId']))
{
	session_id ($_GET['phpSessionId']);
}

session_start();
include ('config.php');
date_default_timezone_set (TIME_ZONE);

/*
	Stupid magic quotes
*/
if (get_magic_quotes_gpc())
{
        $in = array(&$_GET, &$_POST, &$_COOKIE);
        while (list($k,$v) = each($in))
        {
                foreach ($v as $key => $val)
                {
                        if (!is_array($val))
                        {
                                $in[$k][$key] = stripslashes($val);
                                continue;
                        }
                        $in[] =& $in[$k][$key];
                }
        }
        unset($in);
}

/*
	Auto load function:
	Real OOP!
	
	Loads the class /php/group/class.php by calling "new Group_Class ();"
*/
function __autoload ($class_name) 
{

	static $cache;
	
	if (!isset ($cache[$class_name]))
	{

		$cache[$class_name] = true;

		$v = explode ('_', $class_name);
		
		$p = count ($v) - 1;
		$url = BASE_URL.'php';
		$url2 = $url;
		
		foreach ($v as $k => $vv)
		{
		
			if ($k == $p)
			{
				$url .= '/'.$vv.'.php';
				$url2.= '/'.$vv.'/'.$vv.'.php';
			}
			
			else {
				$url .= '/'.$vv;
				$url2 .= '/'.$vv;
			}
		}
	
		if (file_exists ($url))
		{
			include_once ($url);
		}
		
		elseif (file_exists ($url2))
		{
			include_once ($url2);
		}
		
		else {
			//echo ("Class not found: ".$url." or ".$url2);
			return false;
		}
	}
}

function customMail ($target, $subject, $msg)
{
	die ('No mailer defined');
}

// Zoom level

$zoom = isset ($_GET['zoom']) ? $_GET['zoom'] : 100;

switch ($zoom)
{
	case 25:
	case 24:
		$zoom = 24;
		break;
	case 75:
	case 72:
		$zoom = 72;
		break;
	case 50:
	case 150:
		break;
	default:
		$zoom = 100;
		break;
}

define ('MAP_ZOOMLEVEL', $zoom);

/*
$w=200;
$h=100;

function is_even($n)
{
	return(!($n & 1));
}

if ($zoom > 150) $zoom=150;
else if($zoom < 25) $zoom=25;

while ($zoom > 25)
{
	$wt=round(($zoom/$w)*100);
	$ht=round(($zoom/$h)*100);
	$t1=is_even($wt);
	$t2=is_even($ht);
	if($t1+$t2==2) break;
	--$zoom;
}
*/

//customMail ('daedelson@gmail.com', 'test', 'test');
?>
