<?php
	include "funkce/databaze.php"; 
	include "funkce/funkce.php"; 
	if (!empty($_POST["heslo"]) || (isset($_COOKIE["kod"]) && srovnejKody($_COOKIE["kod"]))) {
		session_start();
		$_SESSION["vstup"] = 1;
		$Novy = md5(rand());
		setcookie("kod", $_COOKIE["kod"], time()- 3600*24);
		setcookie("kod", $Novy, time()+ 3600*24*365);
		$RIp = vratIP();
		if ($_POST["heslo"]=="qwup2356") 
			$vlozeno = mysql_query("INSERT INTO pristup VALUES('$Novy', '".$RIp[0]."', NOW())");
		else
			$vlozeno = mysql_query("UPDATE pristup SET kod = '$Novy', ip = '".$RIp[0]."' WHERE kod = '".$_COOKIE["kod"]."' LIMIT 1");
		Header("Location: intranet.php");
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
</head>
<body leftmargin="120" topmargin="16" bottommargin="0" rightmargin="60" onload="window.location.replace("over.php");">
<img src="img/logo.gif" alt="" width="82" height="83" border="0" style="position:absolute; left=0;top=0">
<div class=N4>Správa silnic Olomouckého kraje</div>
<div class=N2>Intranetový server pro interní komunikaci a sdílení dokumentů</div>
<p class=N3>Vítejte na intranetu SSOK</p>
<p class=norm>Tento intranet je interní datová služba určená pouze pro vnitřní potřebu Správy silnic Olomouckého kraje. Přístup k intranetu SSOK získáte 
zadáním své e-mailové adresy jako uživatelské jméno a hesla, které jste si zvolili v ověřovacím formuláři.</p>
<p class="norm chyba">Došlo ke změně v přihlašování k Intranetu. Pokud nemáte vytvořeno přihlašovací heslo, můžete si jej vytvořit v ověřovacím formuláři, který vám bude zaslán na e-mailovou adresu, uvedenou při pokusu o přihlášení.</p>
<p class=norm>Po zadání správného hesla nebo po úspěšném ověření budou již příště stránky intranetu nabíhat automaticky. Máte-li jakékoliv potíže se vstupem do intranetu, obraťte se na:</p>
<a class=mail href="mailto:ochmanova@ssok.cz">paní Jindřišku Ochmannovou</a> Tel.:585 170 337 - dodavatele veškerých písemností, dokumentů a úvodních námětů, nebo na<br>
<a class=mail href="mailto:ulmann@scomeq.cz">Ing. Antonína Ulmanna</a> Tel.:603 469 824 - technického realizovatele této aplikace
</body>
</html>
