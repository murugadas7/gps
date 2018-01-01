<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'fleetsu');
define('G_TITLE', 'Fleetsu');
define('G_LINK', 'http://localhost/gps');
date_default_timezone_set('Asia/Kolkata');
$time = time();
$items_per_group = 7;
if(empty($_SESSION[Count])){
	$_SESSION[Count] = "0";
}