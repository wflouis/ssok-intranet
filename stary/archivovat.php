<?php
session_start();
include "funkce/databaze.php"; 
$result = mysql_query("UPDATE vyb_rizeni SET archiv = '1' WHERE termin < NOW()"); 
mysql_Close($_SESSION["link"]);
?>