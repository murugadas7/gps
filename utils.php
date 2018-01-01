<?php
error_reporting(E_ALL & ~E_NOTICE);
include_once 'api/constants.php';
include_once 'api/connectDB.php';
session_start();
include_once 'api/helperFunctions.php';
include_once 'api/sqlUtilities.php';
include_once 'api/security.php';
$p = "pathReturn";
$route = route($p);