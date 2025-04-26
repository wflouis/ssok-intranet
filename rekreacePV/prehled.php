<?php
function noci($zaznam) {
	$noci = round((strtotime($zaznam["konec"])-strtotime($zaznam["nastup"]))/86400);
	return $noci;
}
function poukaz($zaznam) {
	return round(noci($zaznam)*$zaznam["osob_zam"]*$zaznam["c_zam_pou"])+round(noci($zaznam)*$zaznam["osob_zad"]*$zaznam["c_zad_pou"])+round(noci($zaznam)*$zaznam["osob_ciz"]*$zaznam["c_ciz_pou"])+round(noci($zaznam)*$zaznam["osob_cid"]*$zaznam["c_cid_pou"]);
}
function dph($zaznam) {
 	return round(noci($zaznam)*$zaznam["osob_zam"]*$zaznam["c_zam_dph"])+
		   round(noci($zaznam)*$zaznam["osob_zad"]*$zaznam["c_zad_dph"])+
		   round(noci($zaznam)*$zaznam["osob_ciz"]*$zaznam["c_ciz_dph"])+
		   round(noci($zaznam)*$zaznam["osob_cid"]*$zaznam["c_cid_dph"]);
}
function rekreacni($zaznam) {
 	return noci($zaznam)*(($zaznam["osob_zam"]-$zaznam["inv_zam"])*$zaznam["c_zam_rek"]+$zaznam["osob_zad"]*$zaznam["c_zad_rek"]+($zaznam["osob_ciz"]-$zaznam["inv_ciz"])*$zaznam["c_ciz_rek"]+$zaznam["osob_cid"]*$zaznam["c_cid_rek"]);
}
function celkem($zaznam) {
	return poukaz($zaznam)+dph($zaznam)+rekreacni($zaznam);
}
function kapacitni($zaznam) {
 	return noci($zaznam)*($zaznam["osob_zam"]*$zaznam["c_zam_kap"]+$zaznam["osob_zad"]*$zaznam["c_zad_kap"]+$zaznam["osob_ciz"]*$zaznam["c_ciz_kap"]+$zaznam["osob_cid"]*$zaznam["c_cid_kap"]);
}
include "funkce/databaze.php"; 
include "funkce/funkce.php"; 
$RIp = vratIP();
if (!maPristup())
	exit;
if (!isset($_POST["dat_od"]))
	$_POST["podle_dat"] = $_POST["podle_obj"] = $_POST["objekt"] = 1;
if (empty($_POST["dat_od"]) && empty($_POST["dat_do"])) {
	$month = date( "m", time());
	$year = date( "Y", time());
	$_POST["dat_od"] =  date( "d.m.Y", mktime( 0, 0, 0, $month-1, 1, $year) );
	$_POST["dat_do"] =  date( "d.m.Y", mktime( 0, 0, 0, $month, 0, $year) );
	$_POST["rok"] = "2006";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<script language="JavaScript" src="index.js"></script>
	<script language="JavaScript" src="tisk.js"></script>
	<LINK href="tisk.css" type=text/css rel=stylesheet>
</head>
<body style="background-color : Silver;" <?php if ($_POST["akce"] == "tisk") echo "onload=\"vytiskni()\"";?>>
<h3>Souètový finanèní pøehled rekreaèních poukazù</h3>
<h4>Výbìr poukazù podle:</h4>

<form action="prehled.php" name="prehled" method="post" style="margin: 0;" onKeyDown="enter()" onKeyPress="return cisla('.')">
<table cellpadding="0" cellspacing="0">
<tr><td colspan="2"><input type="checkbox" onchange="zmena()" name="podle_obj" <?php if (isset($_POST["podle_obj"])) echo "checked";?>> objektù</td>
	<td><select name="objekt" style="width: 300" value="<?php echo $_POST["objekt"]?>">
			<?php
				$Puvodni = $_POST["storno"];
				$Sql  = "SELECT * FROM objekty WHERE storno = 0 ORDER by objekt";
				$result = mysqli_query($link, $Sql);
				$Pocet = mysqli_num_rows($result);
				for($i=1; $i<=$Pocet; $i++) {
					$zaznam = mysqli_fetch_assoc($result);
					echo "<option id=\"".$zaznam["objekt"]."\" value=\"$i\" ".($zaznam["objekt"]==$_POST["objekt"]?" selected":"").">".$zaznam["nazev"]."</option>\n";
				}
				$_POST["storno"] = $Puvodni;
				if (empty($_POST["objekt"])) 
					$_POST["objekt"] = 1;
				?>
	</select></td>
	<td rowspan="3" valign="bottom"><input type="hidden" name=akce><input type="submit" name=zobraz value="Zobrazit"><input type="button" name=tisk value="Vytisknout" onClick="kontrola();"></td></tr>
<tr><td><input type="checkbox" onchange="zmena()" name="podle_dat" <?php if (isset($_POST["podle_dat"])) echo "checked";?>> data nástupu</td><td>od</td><td><input type="text" class=cislo name="dat_od" size="11" maxlength="10" onchange="zmena()" value="<?php echo $_POST["dat_od"]?>"> do <input type="text" class=cislo name="dat_do" size="11" maxlength="10" onchange="zmena()" value="<?php echo $_POST["dat_do"]?>"></td></tr>
<tr><td><input type="checkbox" onchange="zmena()" name="podle_pou" <?php if (isset($_POST["podle_pou"])) echo "checked";?>> è.poukazu</td><td>od</td><td><input type="text" class=cislo name="pou_od" size="8" maxlength="7" onchange="zmena()" value="<?php echo $_POST["pou_od"]?>"> do <input type="text" class=cislo name="pou_do" size="8" maxlength="7" onchange="zmena()" value="<?php echo $_POST["pou_do"]?>"> v roce <input type="text" class=cislo name="rok" size="4" maxlength="4" onchange="zmena()" value="<?php echo $_POST["rok"]?>"></td></tr>
</table></form><br>


<!--	
<form action="prehled.php" name="prehled" method="post" style="margin: 0;" onKeyDown="enter()" onKeyPress="return cisla('.')">
<table cellpadding="0" cellspacing="0">
<tr><td colspan="2"><input type="checkbox" onchange="zmena()" name="podle_obj" <?php //if (isset($_POST["podle_obj"])) echo "checked";?>> objektù</td>
	<td><select name="n_objektu" style="width: 300" onchange="zmena(); getObj('objekt').value = getObj('n_objektu').options(getObj('n_objektu').value-1).id;">
			<?php
			/*	$Puvodni = $_POST["storno"];
				$Sql  = "SELECT * FROM objekty WHERE storno = 0 ORDER by objekt";
				$result = mysqli_query($link, $Sql);
				$Pocet = mysqli_num_rows($result);
				for($i=1; $i<=$Pocet; $i++) {
					$zaznam = mysqli_fetch_assoc($result);
					echo "<option id=\"".$zaznam["objekt"]."\" value=\"$i\">".$zaznam["nazev"]."</option>\n";
				}
				$_POST["storno"] = $Puvodni;
				if (empty($_POST["objekt"])) 
					$_POST["objekt"] = 1;
				echo "<script>getObj('n_objektu').value = getObj('".$_POST["objekt"]."').value</script>\n";
				*/?>
	</select><input type="hidden" id="objekt" name="objekt" value="<?php //echo $_POST["objekt"]?>"></td>
	<td rowspan="3" valign="bottom"><input type="hidden" name=akce><input type="submit" name=zobraz value="Zobrazit"><input type="button" name=tisk value="Vytisknout" onClick="kontrola();"></td></tr>
<tr><td><input type="checkbox" onchange="zmena()" name="podle_dat" <?php //if (isset($_POST["podle_dat"])) echo "checked";?>> data nástupu</td><td>od</td><td><input type="text" class=cislo name="dat_od" size="11" maxlength="10" onchange="zmena()" value="<?php echo $_POST["dat_od"]?>"> do <input type="text" class=cislo name="dat_do" size="11" maxlength="10" onchange="zmena()" value="<?php echo $_POST["dat_do"]?>"></td></tr>
<tr><td><input type="checkbox" onchange="zmena()" name="podle_pou" <?php //if (isset($_POST["podle_pou"])) echo "checked";?>> è.poukazu</td><td>od</td><td><input type="text" class=cislo name="pou_od" size="8" maxlength="7" onchange="zmena()" value="<?php echo $_POST["pou_od"]?>"> do <input type="text" class=cislo name="pou_do" size="8" maxlength="7" onchange="zmena()" value="<?php echo $_POST["pou_do"]?>"> v roce <input type="text" class=cislo name="rok" size="4" maxlength="4" onchange="zmena()" value="<?php echo $_POST["rok"]?>"></td></tr>
</table></form><br>
!-->
<span id=oblast>
<table class=prehled cellspacing="0" cellpadding="0" width="1070">
<tr class="zahlavi sirka">
    <td rowspan="2" style="width:45;text-align: left">poukaz</td>
    <td rowspan="2" style="text-align: left">pøíjmení</td>
    <td rowspan="2" style="text-align: left">jméno</td>
    <td rowspan="2" style="width:48">den pøíjezdu</td>
    <td rowspan="2" style="width:48">den odjezdu</td>
    <td rowspan="2" style="width:40">poèet nocí</td>
    <td colspan="4" style="text-align: center; border-bottom : 1 solid White;">poèet osob</td>
    <td rowspan="2">poukaz</td>
    <td rowspan="2">DPH</td>
    <td rowspan="2">poplatek z pobytu</td>
    <td rowspan="2">poukaz celkem</td>
    <td rowspan="2">poplatek kapacitní</td> 
</tr>
<tr class=zahlavi>
    <td style="width:40">zamìst-nanci</td>
    <td style="width:40">dìti</td>
    <td style="width:40">cizí-dospìlí</td>
    <td style="width:40">cizí-dìti</td>
</tr>
<?php
$_SESSION["filtr"] = "1 = 1";
if (isset($_POST["podle_obj"]) && !empty($_POST["objekt"]))
	$_SESSION["filtr"] .= " AND objekt = '".$_POST["objekt"]."'";
if (isset($_POST["podle_dat"]) && (!empty($_POST["dat_od"]) || !empty($_POST["dat_do"])))
	$_SESSION["filtr"] .= " AND nastup >= '".DateCzEn($_POST["dat_od"])."' AND nastup <= '".DateCzEn($_POST["dat_do"])."'";
if (isset($_POST["podle_pou"]) && (!empty($_POST["pou_od"]) || !empty($_POST["pou_do"])))
	$_SESSION["filtr"] .= " AND poukaz >= '".$_POST["pou_od"]."' AND poukaz <= '".$_POST["pou_do"]."' AND rok = '".substr($_POST["rok"],2,2)."'";

//echo "SELECT * FROM poukazy WHERE ".$_SESSION["filtr"]." AND storno = 0 ORDER BY poukaz LIMIT 100";
$result = mysqli_query($link, "SELECT * FROM poukazy WHERE ".$_SESSION["filtr"]." AND storno = 0 ORDER BY poukaz LIMIT 100");
$Pocet = mysqli_num_rows($result);
if ($_SESSION["filtr"] == "1 = 1" || $Pocet == 100) {
	$Pocet = 0;
	echo "<tr><td colspan=\"12\" style=\"text-align: left\">Výsledek dotazu obsahuje pøíliš mnoho záznamù. Zadejte pøísnìjší kritéria výbìru!</td></tr>\n";
}	
if ($Pocet > 0) {
	$nociCelkem = $zamCelkem = $zadCelkem = $cizCelkem = $cidCelkem = 0;
	$pouCelkem = $dphCelkem = $rekCelkem = $kapCelkem = 0;
	for($i=1; $i<=$Pocet; $i++):
		$zaznam = mysqli_fetch_assoc($result);
		if ($i%2 == 0)
			echo "<tr class=\"sudy\">\n";
		else	
			echo "<tr class=\"lichy\">\n";
		echo "<td style=\"text-align: left\">".$zaznam["zkratka"].$zaznam["rok"].str_pad($zaznam["poukaz"], 3, "0", STR_PAD_LEFT)."</td>\n";
		echo "<td style=\"text-align: left\">".$zaznam["prijmeni"]."</td>\n";
		echo "<td style=\"text-align: left\">".$zaznam["jmeno"]."</td>\n";
		echo "<td>".DateEnCz($zaznam["nastup"])."</td>\n";
		echo "<td>".DateEnCz($zaznam["konec"])."</td>\n";
		echo "<td>".noci($zaznam)."</td>\n";
		echo "<td>".$zaznam["osob_zam"]."</td>\n";
		echo "<td>".$zaznam["osob_zad"]."</td>\n";
		echo "<td>".$zaznam["osob_ciz"]."</td>\n";
		echo "<td>".$zaznam["osob_cid"]."</td>\n";
		echo "<td>".number_format(poukaz($zaznam),2,"."," ")." Kè</td>\n";
		echo "<td>".number_format(dph($zaznam),2,"."," ")." Kè</td>\n";
		echo "<td>".number_format(rekreacni($zaznam),2,"."," ")." Kè</td>\n";
		echo "<td>".number_format(celkem($zaznam),2,"."," ")." Kè</td>\n";
		echo "<td>".number_format(kapacitni($zaznam),2,"."," ")." Kè</td></tr>\n";
		$nociCelkem += noci($zaznam);
		$zamCelkem  += $zaznam["osob_zam"];
		$zadCelkem  += $zaznam["osob_zad"];
		$cizCelkem  += $zaznam["osob_ciz"];
		$cidCelkem  += $zaznam["osob_cid"];
		$pouCelkem += poukaz($zaznam);
	 	$dphCelkem += dph($zaznam);
	 	$rekCelkem += rekreacni($zaznam);
	 	$kapCelkem += kapacitni($zaznam); 
	endfor;
}	
echo "<tr class=zahlavi>\n";
    echo "<td colspan=\"5\" style=\"text-align: left\">Celkem</td>\n";
    echo "<td>$nociCelkem</td>\n";
    echo "<td>$zamCelkem</td>\n";
    echo "<td>$zadCelkem</td>\n";
    echo "<td>$cizCelkem</td>\n";
    echo "<td>$cidCelkem</td>\n";
    echo "<td>".number_format($pouCelkem,2,"."," ")." Kè</td>\n";
    echo "<td>".number_format($dphCelkem,2,"."," ")." Kè</td>\n";
    echo "<td>".number_format($rekCelkem,2,"."," ")." Kè</td>\n";
    echo "<td>".number_format($pouCelkem + $dphCelkem + $rekCelkem,2,"."," ")." Kè</td>\n";
    echo "<td>".number_format($kapCelkem,2,"."," ")." Kè</td>\n";
?>
</tr>
</table></span>
</body>
</html>
