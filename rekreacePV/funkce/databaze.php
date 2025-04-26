<?php
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

ini_set('default_charset', 'windows-1250');
$db_server = "localhost";	
$db_user = "root";
$db_passwd = "Scom15451242";
$db_name = "rekreacePV";
$link = mysqli_connect($db_server, $db_user, $db_passwd) or die("Spojení se serverem selhalo. Zkuste to prosím pozdìji!");
mysqli_select_db($link,$db_name) or die("Požadovaná databáze nenalezena. Zkuste to prosím pozdìji!");
?>
