<?php
include "funkce/funkce.php"; 
if (!maPristup("S")) 
	exit;
include "funkce/databaze.php"; 
if (empty($_POST["Sloupec"]) || $_SESSION["smer"]==" desc") {
	$_SESSION["smer"] = " asc";
	if (empty($_POST["Sloupec"]))
		$_POST["Sloupec"] = 1; 
} else
	$_SESSION["smer"] = " desc";
if (empty($_POST["Adresar"]))
	$_POST["Adresar"] = "bezpecnost";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="jquery.js"></script>
	<script language="JavaScript" src="funkce.js"></script>
</head>
<body leftmargin="20" topmargin="20" bottommargin="20" rightmargin="20">
<p class=N3>Bezpe�nost pr�ce</p>
<p class=norm>Dokumenty jsou k dispozici v p�vodn�m form�tu aplikac�, ve kte�ch byly vytvo�eny. Jsou zde povoleny soubory s n�sleduj�c�mi p��ponami: doc, xls, jpg, gif, tif, txt, zip, pdf. Kliknut�m na z�hlav� sloupce dojde s se�azen� z�znam� dle p��slu�n�ho sloupce. Jste-li k intranetu p�ipojeni nap�. modemem, bu�te trp�liv� p�i zobrazov�n� v�t��ch soubor�. Sledujte �daj o velikosti souboru, nap�. soubor o velikosti 200 000 byt� se u modemov�ho spojen� zobraz� a� za 40s.</p>
<table id=seznam cellpadding="3" cellspacing="0">
<tr class=HlTab><td onClick="serad(1)">N�zev souboru 
<?php 
	if ($_POST["Sloupec"]==1)
		if ($_SESSION["smer"]==" asc")
	 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
	 	else
	 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
    echo "</td><td onClick=\"serad(2)\">datum ";
	if ($_POST["Sloupec"]==2)
		if ($_SESSION["smer"]==" asc")
	 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
	 	else
	 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
?>
</td><td align="right">velikost</td></tr>
<?php
	obsahAdr("/share/intranet/".$_POST["Adresar"],$_POST["Sloupec"],$_SESSION["smer"]);
?>
</table>
<form action="bezpecnost.php" method="post" name="razeni" id="razeni">
<input type="hidden" name="akce" value="">
<input type="hidden" name="Sloupec" value="<?php echo $_POST["Sloupec"]?>">
<input type="hidden" name="Adresar" value="<?php echo $_POST["Adresar"]?>">
</form>
</body>
</html>
