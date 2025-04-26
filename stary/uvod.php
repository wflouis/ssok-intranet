<?php
include "funkce/funkce.php"; 
if (!maPristup()) 
	exit;
include "funkce/databaze.php"; 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
</head>
<body leftmargin="20" topmargin="20" bottommargin="20" rightmargin="20">
<p class=N3>V�tejte na Intranetu SSOK</p>
<p class=N2>Se sv�mi p�ipom�nkami se m��ete obracet na:</p>
<table cellpadding="0" cellspacing="0"><tr><td width="200"><a class=N0 href="mailto:kozakova@ssok.cz">pan� Zlatku Koz�kovou</a></td><td> - spr�vce obsahu</td></tr>
<tr><td><a class=N0 href="mailto:ulmann@scomeq.cz">Ing. Anton�na Ulmanna</a></td><td> - technick�ho realizovatele a spr�vce t�to aplikace</td></tr></table>
<p class=N3>Aktuality a ve�ejn� sd�len�</p>
<table class="zpravy" cellpadding="3" cellspacing="0" width="600">
<?php 
	$zpravy=mysqli_query($_SESSION["link"],"DELETE FROM zpravy WHERE datum < '".date("Y.m.d",time()-3600*24*90)."'");
	$Sql  = "SELECT zpravy.*, jmeno FROM zpravy JOIN seznam ON zpravy.id_jmeno=seznam.id_jmeno ORDER BY datum desc";
	$zpravy=mysqli_query($_SESSION["link"],$Sql);
	$pocet=mysqli_num_rows($zpravy);
	for($i=1; $i<=$pocet; $i++) {
		$zaznam = mysqli_fetch_assoc($zpravy);
		echo "<tr class=suda><td width=\"240\">Ze dne: ";
		echo DateEnCz(substr($zaznam["datum"],0,10)).substr($zaznam["datum"],10,9)."</td><td width=\"470\">Autor: ".$zaznam["jmeno"]."</td></tr>\n";
		echo "<tr><td colspan=\"2\" class=poznamka>Text: ".$zaznam["text"]."</td></tr>\n";
	}
?>
</table>
</body>
</html>


