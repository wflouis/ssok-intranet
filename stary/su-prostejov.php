<?php
session_start();
include "funkce/databaze.php"; 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Správa silnic Olomouckého kraje</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
	<LINK href="http://www.ssok.cz/ssok.css" type=text/css rel=stylesheet>
</head>
<body leftmargin="20" topmargin="20" rightmargin="20">
<?php
$strediska = array("SUJi","CePv","CeKo","CePr","CeHr");
foreach($strediska as $stredisko) {
	echo "<table class=seznam  cellspacing=\"0\" cellpadding=\"0\" bordercolorlight=\"#EAFDFD\" id=\"ssok\">";
	@$result = mysql_query("SELECT str.*, nazev FROM strediska str, seznam_str sez where str.stredisko=sez.zkratka and stredisko='$stredisko' ORDER BY poradi");
	echo "<tr><td>";
	$prvni = true;
	while ($radek = mysql_fetch_assoc($result)) {
		if ($prvni) {
			echo "<div class=nadpis>".$radek["nazev"]."</div>";
			$prvni = false;
		}
		if (ereg("@",$radek["text"]))
			echo $radek["nadpis"]." <a href=\"mailto:".$radek["text"]."\">".$radek["text"]."</a><br>";
		else
			echo $radek["nadpis"]." ".str_replace(",","<br>",$radek["text"])."<br>";
	}
	echo "</td><td colspan=\"3\"></td></tr>";
	
	echo "<tr class=nadpis><td>Jméno</td><td>funkce</td><td>telefon</td><td>e-mail</td></tr>";
	@$result = mysql_query("SELECT * FROM seznam where stredisko='$stredisko' and internet = '1' ORDER BY jmeno");
	$cisloRadku = 0;
	while ($radek = mysql_fetch_assoc($result)) {
	  if ($cisloRadku%2==0)
	    echo "<TR  class=licha>\n";
	  else
	    echo "<TR  class=suda>\n";
	  echo "<TD>".$radek["jmeno"]."</TD>\n";
	  echo "<TD>".$radek["funkce"]."</TD>\n";
	  echo "<TD>".$radek["telefon"]."</TD>\n";
	  echo "<TD><a href=\"mailto:".$radek["email"]."\">".$radek["email"]."</a></TD>\n";
	  echo "</TR>\n";
	  $cisloRadku += 1;
	}
	echo "</table><br>\n";
}
?>
</body>
</html>
