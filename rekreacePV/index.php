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
<h1>Rekrea�n� za��zen� SSOK</h1>
<table cellpadding="0" cellspacing="0" width="600"><tr><td><p>Tento program je ur�en pro intern� ��ely Spr�vy silnic Olomouck�ho kraje! P��stup k programu z�sk�te zad�n�m <strong>hesla</strong>:</p>
<form action="index.php" method="post">
<div align="center"><input type="password" name="heslo" size="10" maxlength="8">&nbsp;&nbsp;&nbsp; 
<input type="submit" value="P�ihl�sit"></div>
</form>
<p>Po zad�n� spr�vn�ho hesla budou ji� p��t� str�nky nab�hat automaticky. Nezn�te-li p��stupov� heslo nebo m�te-li jak�koliv pot�e se vstupem do intranetu, obra�te se na spr�vce aplikace:</p>
<p>Ing. Anton�n Ulmann, Tel.:603 469 824</td></tr></table>
</body>
</html>
