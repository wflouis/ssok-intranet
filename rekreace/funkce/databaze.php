<?php
$db_server = "localhost";	
$db_user = "root";
$db_passwd = "Scom15451242";
$db_name = "rekreace";
mysql_connect($db_server, $db_user, $db_passwd) or die("Spojen� se serverem selhalo. Zkuste to pros�m pozd�ji!");
//mysql_query("SET NAMES cp1250");
mysql_select_db($db_name) or die("Po�adovan� datab�ze nenalezena. Zkuste to pros�m pozd�ji!");
?>
