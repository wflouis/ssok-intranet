<?php
include "funkce/funkce.php"; 
if (!maPristup()) 
	exit;
include "funkce/databaze.php"; 
if (!isset($_POST["najit"]))
	$_POST["najit"] = "";
if (!isset($_POST["akce"]))
	$_POST["akce"] = "";
if (empty($_POST["Sloupec"]) || $_SESSION["smer"]==" desc") {
	$_SESSION["smer"] = " asc";
	if (empty($_POST["Sloupec"]))
		$_POST["Sloupec"] = 1; 
} else
	$_SESSION["smer"] = " desc";
if (empty($_POST["Adresar"]))
	$_POST["Adresar"] = "predpisy";
	
if ($_POST["akce"]=="Na��st" && isset($_FILES['soubor']))   
	move_uploaded_file($_FILES['soubor']['tmp_name'],"/share/intranet/".$_POST["Adresar"]."/".$_FILES['soubor']['name']);
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
<p class=N3>Intern� dokumenty organizace</p>
<p class=norm>Dokumenty jsou k dispozici v p�vodn�m form�tu aplikac�, ve kter�ch byly vytvo�eny. Jsou zde povoleny soubory s n�sleduj�c�mi p��ponami: doc, xls, jpg, gif, tif, txt, zip, pdf. Kliknut�m na z�hlav� sloupce dojde s se�azen� z�znam� dle p��slu�n�ho sloupce. Jste-li k intranetu p�ipojeni nap�. modemem, bu�te trp�liv� p�i zobrazov�n� v�t��ch soubor�. Sledujte �daj o velikosti souboru, nap�. soubor o velikosti 200 000 byt� se u modemov�ho spojen� zobraz� a� za 40s.</p>
<form action="predpisy.php" method="post" enctype="multipart/form-data" name="razeni" id="razeni">
<?php
if (maPristup("P",true)) {
	echo "Za�adit nov� dokument do aktu�ln�ho adres��e: <input type=\"file\" name=\"soubor\" size=\"50\"> <input type=\"submit\" name=\"akce\" value=\"Na��st\"><br><br>";
}
?>
Vyhledat dokumenty obsahuj�c� v n�zvu text: <input type="text" name="najit" value="<?php echo $_POST["najit"]?>"> <input type="submit" name="akce" value="Vyhledat"><br><br>
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
	obsahAdr("/share/intranet/".$_POST["Adresar"],$_POST["Sloupec"],$_SESSION["smer"],$_POST["najit"]);
?>
</table>
<input type="hidden" name="Sloupec" value="<?php echo $_POST["Sloupec"]?>">
<input type="hidden" name="Adresar" value="<?php echo $_POST["Adresar"]?>">
</form>
</body>
</html>
