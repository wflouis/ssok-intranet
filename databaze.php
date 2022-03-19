<?php
$link = @mysqli_connect("localhost", "root", "centos","ssok") or die("Spojení se serverem selhalo. Zkuste to prosím později!");
mysqli_set_charset($link, "utf8");
?>
