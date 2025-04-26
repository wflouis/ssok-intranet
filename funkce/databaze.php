<?php
$db_server = "localhost";	
$db_user = "root";
$db_passwd = "Scom15451242";
$db_name = "ssok";
$_SESSION["link"] = mysqli_connect($db_server, $db_user, $db_passwd) or die("Spojení se serverem selhalo. Zkuste to prosím pozdìji!");
mysqli_query($_SESSION["link"],"SET NAMES cp1250");
mysqli_select_db($_SESSION["link"],$db_name) or die("Požadovaná databáze nenalezena. Zkuste to prosím pozdìji!");
?>
