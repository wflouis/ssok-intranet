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
<div class=N4>Spr�va silnic Olomouck�ho kraje</div>
<div class=N2>Intranetov� server pro intern� komunikaci a sd�len� dokument�</div>
<p class=N3>V�tejte na intranetu SSOK</p>
<p class=norm>Tento intranet je intern� datov� slu�ba ur�en� pouze pro vnit�n� pot�ebu Spr�vy silnic Olomouck�ho kraje. P��stup k intranetu SSOK z�sk�te 
zad�n�m sv� e-mailov� adresy jako u�ivatelsk� jm�no a hesla, kter� jste si zvolili v ov��ovac�m formul��i.</p>
<p class="norm chyba">Do�lo ke zm�n� v p�ihla�ov�n� k Intranetu. Pokud nem�te vytvo�eno p�ihla�ovac� heslo, m��ete si jej vytvo�it v ov��ovac�m formul��i, kter� v�m bude zasl�n na e-mailovou adresu, uvedenou p�i pokusu o p�ihl�en�.</p>
<p class=norm>Po zad�n� spr�vn�ho hesla nebo po �sp�n�m ov��en� budou ji� p��t� str�nky intranetu nab�hat automaticky. M�te-li jak�koliv pot�e se vstupem do intranetu, obra�te se na:</p>
<a class=mail href="mailto:ochmanova@ssok.cz">pan� Jind�i�ku Ochmannovou</a> Tel.:585 170 337 - dodavatele ve�ker�ch p�semnost�, dokument� a �vodn�ch n�m�t�, nebo na<br>
<a class=mail href="mailto:ulmann@scomeq.cz">Ing. Anton�na Ulmanna</a> Tel.:603 469 824 - technick�ho realizovatele t�to aplikace
</body>
</html>
