<?php
include "funkce/funkce.php"; 
if (!maPristup("A")) 
	exit;
include "funkce/databaze.php"; 
if (empty($_POST["Sloupec"]) || $_SESSION["smer"]==" desc") {
	$_SESSION["smer"] = " asc";
	if (empty($_POST["Sloupec"]))
		$_POST["Sloupec"] = 1; 
} else
	$_SESSION["smer"] = " desc";
if (empty($_POST["Adresar"]))
	$_POST["Adresar"] = "sdeleni";
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="funkce.js"></script>
</head>
<body leftmargin="20" topmargin="20" bottommargin="20" rightmargin="20">
<p class=N3>Interní sdělení</p>
<p class=norm>Dokumenty jsou k dispozici v původním formátu aplikací, ve kterých byly vytvořeny. Jsou zde povoleny soubory s následujícími příponami: doc, xls, jpg, gif, tif, txt, zip, pdf. Kliknutím na záhlaví sloupce dojde s seřazení záznamů dle příslušného sloupce. Jste-li k intranetu připojeni např. modemem, buďte trpěliví při zobrazování větších souborů. Sledujte údaj o velikosti souboru, např. soubor o velikosti 200 000 bytů se u modemového spojení zobrazí až za 40s.</p>
<table id=seznam cellpadding="3" cellspacing="0">
<tr class=HlTab><td onClick="serad(1)">Název souboru 
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
	obsahAdr("/share/Intranet/".$_POST["Adresar"],$_POST["Sloupec"],$_SESSION["smer"]);
?>
</table>
<form name=razeni action="sdeleni.php" method="post">
<input type="hidden" name="Sloupec" value="<?php echo $_POST["Sloupec"]?>">
<input type="hidden" name="Adresar" value="<?php echo $_POST["Adresar"]?>">
</form>
</body>
</html>
