<?php
	include "funkce/databaze.php"; 
	include "funkce/funkce.php"; 
	if ((isset($_POST["heslo"]) && $_POST["heslo"]=="relax") || (isset($_COOKIE["kod"]) && srovnejKody($_COOKIE["kod"]))) {
		session_start();
		$_SESSION["vstup"] = 1;
		$Novy = md5(rand());
		setcookie("kod", $_COOKIE["kod"], time()- 3600*24);
		setcookie("kod", $Novy, time()+ 3600*24*365);
		$RIp = vratIP();
		if ($_POST["heslo"]=="relax") 
			$vlozeno = mysqli_query($link, "INSERT INTO pristup VALUES('$Novy', '".$RIp[0]."', NOW())");
		else
			$vlozeno = mysqli_query($link, "UPDATE pristup SET kod = '$Novy', ip = '".$RIp[0]."' WHERE kod = '".$_COOKIE["kod"]."' LIMIT 1");
		Header("Location: rekreace.html");
	}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="index.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="index.js"></script>
</head>
<body onload="getObj('heslo').focus();">
<h1>Rekreační zařízení SSOK</h1>
<table cellpadding="0" cellspacing="0" width="600"><tr><td><p>Tento program je určen pro interní účely Správy silnic Olomouckého kraje! Přístup k programu získáte zadáním <strong>hesla</strong>:</p>
<form action="index.php" method="post">
<div align="center"><input type="password" name="heslo" size="10" maxlength="8">&nbsp;&nbsp;&nbsp; 
<input type="submit" value="Přihlásit"></div>
</form>
<p>Po zadání správného hesla budou již příště stránky nabíhat automaticky. Neznáte-li přístupové heslo nebo máte-li jakékoliv potíže se vstupem do intranetu, obraťte se na správce aplikace:</p>
<p>Ing. Antonín Ulmann, Tel.:603 469 824</td></tr></table>
</body>
</html>
