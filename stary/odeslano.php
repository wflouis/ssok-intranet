<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">

<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="http://www.ssok.cz/ssok.css" type=text/css rel=stylesheet>
</head>
<style>
	span {
		color: red;
	}
	body {
		margin: 5ex;
	}
</style>
<body>
<?php
if ($_GET["stav"]==1)
	echo "Dokumentace k vybranému výběrovému řízení byla zaslanána na uvedenou e-mailovou adresu.";
else
	echo "<span>Bohužel se nepodařilo dokumentaci odeslat. Obraťte se na kontaktní osobu.</span>";
?>
</html>
</body>
