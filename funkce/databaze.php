<?php
$db_server = "localhost";	
$db_user = "root";
$db_passwd = "Scom15451242";
$db_name = "ssok";
$_SESSION["link"] = mysqli_connect($db_server, $db_user, $db_passwd) or die("Spojen� se serverem selhalo. Zkuste to pros�m pozd�ji!");
mysqli_query($_SESSION["link"],"SET NAMES cp1250");
mysqli_select_db($_SESSION["link"],$db_name) or die("Po�adovan� datab�ze nenalezena. Zkuste to pros�m pozd�ji!");
?>
