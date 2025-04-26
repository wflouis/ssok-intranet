<?php
function celkCena($zaznam) {
	$noci = round((strtotime($zaznam["konec"])-strtotime($zaznam["nastup"]))/86400);
	$celkem = 
	round($noci*$zaznam["osob_zam"]*($zaznam["c_zam_pou"]+$zaznam["c_zam_dph"]))+
	round($noci*($zaznam["osob_zam"]-$zaznam["inv_zam"])*$zaznam["c_zam_rek"])+
	round($noci*$zaznam["osob_zad"]*($zaznam["c_zad_pou"]+$zaznam["c_zad_dph"]+$zaznam["c_zad_rek"]))+
	round($noci*$zaznam["osob_ciz"]*($zaznam["c_ciz_pou"]+$zaznam["c_ciz_dph"]))+
	round($noci*($zaznam["osob_ciz"]-$zaznam["inv_ciz"])*$zaznam["c_ciz_rek"])+
	round($noci*$zaznam["osob_cid"]*($zaznam["c_cid_pou"]+$zaznam["c_cid_dph"]+$zaznam["c_cid_rek"]));
	return $celkem;
}
function soucetRadku($radek) {
	return number_format($_POST["c_".$radek."_pou"]+$_POST["c_".$radek."_dph"]+$_POST["c_".$radek."_rek"],2,"."," ");
}
function razeni($sloupec) {
	$text = "";
	if ($_POST["sloupec"] == $sloupec)
		if ($_SESSION["smer"] == " DESC")
			$text = "<img src=\"obrazky/dolu.gif\">";
		else 
			$text = "<img src=\"obrazky/nahoru.gif\">";
	return $text;
}
include "funkce/databaze.php"; 
include "funkce/funkce.php"; 
$RIp = vratIP();
if (!maPristup()) 
	exit;
if (empty($_SESSION["sloupec"])) {
	$_POST["sloupec"] = $_SESSION["sloupec"] = "rok DESC,poukaz";
	$_POST["strana"] = $_SESSION["strana"] = 1;
	$_SESSION["smer"]  = " DESC";
}
if (empty($_POST["sloupec"])) 
	$_POST["sloupec"] = $_SESSION["sloupec"];
if (empty($_POST["strana"])) 
	$_POST["strana"] = $_SESSION["strana"];

define("pocStran", 10);
define("pocRadku", 20);

if (!isset($_POST["akce"])) 
	$_POST["akce"] = "";
if (!isset($_POST["poukaz"])) 
	$_POST["poukaz"] = "";
if (!isset($_POST["objekt"])) 
	$_POST["objekt"] = "";

switch ($_POST["akce"]) {
	case "serad":  
		if ($_SESSION["sloupec"] == $_POST["sloupec"])
			if ($_SESSION["smer"] == " DESC")
				$_SESSION["smer"] = "";
			else
				$_SESSION["smer"] = " DESC";
		else
			$_SESSION["sloupec"] = $_POST["sloupec"];
		break;
	case "nacti": 
		$_POST["objekt"] = substr($_POST["poukaz"],0,2);
		$_POST["rok"] = substr($_POST["poukaz"],2,2);
		$_POST["poukaz"] = substr($_POST["poukaz"],4,3);
		break;
	case "novy":
		$_SESSION["zkratka"] = ""; break;
	case "ulozit":
		if (empty($_POST["objekt"])) {
			$_POST["akce"] = "";
		} else {
			$result = mysqli_query($link, "SELECT zkratka FROM objekty WHERE storno = 0 and objekt = '".$_POST["objekt"]."'");
			if ($zaznam = mysqli_fetch_assoc($result)) {
				$_POST["zkratka"] = $zaznam['zkratka']; 
			}
		}
}

if (!empty($_POST["nastup"])) {
	$_POST["nastup"] = DateCzEn($_POST["nastup"]);
	$_POST["konec"] = DateCzEn($_POST["konec"]);
} else {
	$_POST["nastup"] = Date("Y-m-d",Time());
	$_POST["konec"] = Date("Y-m-d",Time());
}

foreach ($_SESSION as $klic => $hodnota) 
	if (substr($klic,0,2)=="c_")
		$_POST[$klic] = $hodnota;
$_POST["rok"] = $_SESSION["rok"];
//$_POST["zkratka"] = $_SESSION["zkratka"];
if ($_POST["akce"]=="stornovat")
	$_POST["storno"] = !$_SESSION["storno"];
$neniPole = array("akce","n_objektu","ulozit","tarif","tisk","stornovat","smazat","serad","poukaz","sloupec","strana","inv_zad","inv_cid");
//echo "Parametry: ",$_POST["akce"],",","poukazy",",","objekt",",",$_POST["objekt"],",","rok",",",$_POST["rok"],",","poukaz",",",$_POST["poukaz"];
akceSDbf($_POST["akce"],"poukazy",$neniPole,"poukaz",$_POST["poukaz"],"objekt",$_POST["objekt"],"rok",$_POST["rok"]);
if (empty($_POST["objekt"]) && !empty($_POST["n_objektu"])) {
	$_POST["objekt"] = $_POST["n_objektu"];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="cs">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="index.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="index.js"></script>
	<script language="JavaScript" src="poukaz.js"></script>
	<script>
		sazby = {
<?php	if (empty($_POST["poukaz"]) || $_POST["akce"]=="tarif") {
		$neniPole = array("akce","ulozit","tisk","smazat","serad","objekt");
		akceSDbf("nacti","objekty",$neniPole,"objekt",$_POST["objekt"]);
	}
	foreach ($_POST as $klic => $hodnota) 
		if (substr($klic,0,2)=="c_")
			$_SESSION[$klic] = $hodnota;
	$_SESSION["rok"] = $_POST["rok"];
	$_SESSION["poukaz"] = $_POST["poukaz"];	//pro tisk;
	$_SESSION["objekt"] = $_POST["objekt"];	//pro tisk;
	$_SESSION["zkratka"] = $_POST["zkratka"];
	$_SESSION["storno"] = $_POST["storno"];
if (isset($_POST["c_zam_pou"])) {
	echo "c_zam_pou : '".$_POST["c_zam_pou"]."',\n";
	echo "c_zam_dph : '".$_POST["c_zam_dph"]."',\n";
	echo "c_zam_rek : '".$_POST["c_zam_rek"]."',\n";
	echo "c_zad_pou : '".$_POST["c_zad_pou"]."',\n";
	echo "c_zad_dph : '".$_POST["c_zad_dph"]."',\n";
	echo "c_zad_rek : '".$_POST["c_zad_rek"]."',\n";
	echo "c_ciz_pou : '".$_POST["c_ciz_pou"]."',\n";
	echo "c_ciz_dph : '".$_POST["c_ciz_dph"]."',\n";
	echo "c_ciz_rek : '".$_POST["c_ciz_rek"]."',\n";
	echo "c_cid_pou : '".$_POST["c_cid_pou"]."',\n";
	echo "c_cid_dph : '".$_POST["c_cid_dph"]."',\n";
	echo "c_cid_rek : '".$_POST["c_cid_rek"]."'\n";
}
?>
		}
</script>
</head>
<body 
<?php  $zalozka = array("nacti","novy","tarif","ulozit");
	if (in_array($_POST["akce"],$zalozka))
		echo "onload=\"prepni('o2')\";"; 
	else 
		echo "onload=\"prepni('o1')\";"; ?>>
<h3 id="nadpisS" style="display: none;">Seznam rekrea�n�ch poukaz�</h3>
<h3 id="nadpisP" style="display: none;">Rekrea�n� poukaz �. <?php echo $_POST["zkratka"].$_POST["rok"].str_pad($_POST["poukaz"], 3, "0", STR_PAD_LEFT);?></h3>
<table cellpadding="0" cellspacing="0"><tr><td>
<?php
$result = mysqli_query($link, "SELECT count(poukaz) as pocet FROM poukazy WHERE 1 = 1");
$zaznam = mysqli_fetch_assoc($result);
$posledni = ceil($zaznam["pocet"]/pocRadku);
switch ($_POST["akce"]) {
	case "zacatek": $_POST["strana"] = 1; break;
	case "vlevo": $_POST["strana"] = ceil($_POST["strana"]/pocStran)*pocStran-pocStran; break;
	case "vpravo": $_POST["strana"] = ceil($_POST["strana"]/pocStran)*pocStran+1; break;
	case "konec": $_POST["strana"] = $posledni; break;
}
$_SESSION["strana"] = $_POST["strana"];
?>
<table id="posun" align="right"><tr><td onClick="navigace()">
<?php 
if ($_POST["strana"] > pocStran)
	echo "<img src=\"obrazky\\zacatek.gif\"> <img src=\"obrazky\\vlevo.gif\">";
$do = ceil($_POST["strana"]/pocStran)*pocStran;
$od = $do - pocStran + 1;
for ($i=$od;$i<=min($do,$posledni);$i++) 
	if ($i == $_POST["strana"])
		echo " <a class=aktivni href=\"#\">$i</a>";
	else
		echo " <a href=\"#\">$i</a>";
if ($do < $posledni)
	echo " <img src=\"obrazky\\vpravo.gif\"> <img src=\"obrazky\\konec.gif\">";
?>
</td></tr></table>
<table class="ouska" cellpadding="0" cellspacing="0" width="160" onClick="prepni(event.srcElement.id);"><tr align="center" style="font-weight : bold;"><td id="o1" class=pasOusko height="30">Seznam</td><td id="o2" class=pasOusko>Poukaz</td></tr></table>
<form action="poukazy.php" method="post" name="vyber" id="vyber" style="margin: 0; display : none;">
<table cellpadding="1" cellspacing="1" class=tabulka onMouseOver="rozsvit()" onMouseOut="zhasni()" onClick="nacti('poukaz')">
<tr class=zahlavi style="text-align : left;"><td>Poukaz <?php echo razeni("rok DESC,poukaz");?></td><td>od <?php echo razeni("nastup");?></td><td>do <?php echo razeni("konec");?></td><td style="width:180;">jm�no <?php echo razeni("prijmeni");?></td><td>osob</td><td style="width:85">cena</td></tr>
<?php 	
$result = mysqli_query($link, "SELECT * FROM poukazy WHERE 1=1 ORDER BY ".$_POST["sloupec"].$_SESSION["smer"]." LIMIT ".(($_POST["strana"]-1)*pocRadku).",".pocRadku);
//echo "SELECT * FROM poukazy WHERE 1=1 ORDER BY ".$_POST["sloupec"].$_SESSION["smer"]." LIMIT ".(($_POST["strana"]-1)*pocRadku).",".pocRadku;
$Pocet = mysqli_num_rows($result);
if ($Pocet > 0) {
	for($i=1; $i<=$Pocet; $i++):
		$zaznam = mysqli_fetch_assoc($result);
		if ($i%2 == 0)
			echo "<tr class=\"sudy";
		else	
			echo "<tr class=\"lichy";
		if ($zaznam["storno"]) echo " storno";
		if ($zaznam["objekt"]==$_POST["objekt"] && $zaznam["rok"]==$_POST["rok"] && $zaznam["poukaz"]==$_POST["poukaz"]) echo " vybrany";
		echo "\" id=\"".str_pad($zaznam["objekt"], 2, "0", STR_PAD_LEFT).$zaznam["rok"].str_pad($zaznam["poukaz"], 3, "0", STR_PAD_LEFT)."\">\n<td>".$zaznam["zkratka"].$zaznam["rok"].str_pad($zaznam["poukaz"], 3, "0", STR_PAD_LEFT)."</td>\n";
		echo "<td>".DateEnCz($zaznam["nastup"])."</td>\n";
		echo "<td>".DateEnCz($zaznam["konec"])."</td>\n";
		echo "<td align=\"left\">".$zaznam["prijmeni"]." ".$zaznam["jmeno"]."</td>\n";
		echo "<td>".($zaznam["osob_zam"]+$zaznam["osob_ciz"])."/".($zaznam["osob_zad"]+$zaznam["osob_cid"])."</td>\n";
		echo "<td>".number_format(celkCena($zaznam),2,"."," ")." K�</td></tr>\n";
	endfor;
}	
?>
</table><br>
<div align="right" onClick="proved('vyber')">
<input type="hidden" id="sloupec" name="sloupec">
<input type="hidden" id="strana" name="strana">
<input type="hidden" id="akce" name="akce">
<button name="novy">Nov�</button>
</div>
</form>

<form action="poukazy.php" method="post" name="polozka" id="polozka" onKeyDown="enter()" onKeyPress="return cisla('.')" style="margin: 0; display : none;">
<table cellpadding="0" cellspacing="0">
	<tr><td colspan="2" height="8"></td></tr>
	<tr><td>
		<table cellpadding="0" cellspacing="0">
			<tr><td>Objekt:</td><td>
			<select id="n_objektu" name="n_objektu" style="width:307" onchange="getObj('objekt').value=this.value; getObj('tarif').click()" <?php if (!empty($_POST["poukaz"])) echo "disabled"; ?>>
			<?php	$Puvodni = $_POST["storno"];
				$Sql  = "SELECT * FROM objekty WHERE storno = 0 ORDER by objekt";
				$result = mysqli_query($link, $Sql);
				$Pocet = mysqli_num_rows($result);
				for($i=1; $i<=$Pocet; $i++) {
					$zaznam = mysqli_fetch_assoc($result);
					echo "<option id=\"".$zaznam["objekt"]."\" value=\"$i\">".$zaznam["nazev"]."</option>\n";
				}
				$_POST["storno"] = $Puvodni;
				if (!empty($_POST["objekt"])) echo "<script>getObj('n_objektu').value = getObj('".$_POST["objekt"]."').value</script>\n"; ?>
			</select>
			<input type="hidden" id="poukaz" name="poukaz" value="<?php echo $_POST["poukaz"]?>">
			<input type="hidden" id="objekt" name="objekt" value="<?php echo $_POST["objekt"]?>"></td></tr>
			<tr><td width="100">Zam�stnanec:</td><td><input type="text" id="nazev_zam" name="nazev_zam" size="42" maxlength="40" onchange="top.zmeneno = true;" value="<?php echo $_POST["nazev_zam"]?>"></td></tr>
			<tr><td>Ciz�:</td><td><input type="text" id="nazev_ciz" name="nazev_ciz" size="42" maxlength="40" onchange="top.zmeneno = true;" value="<?php echo $_POST["nazev_ciz"]?>"></td></tr>
		</table>
	</td>
	<td>
		<table class=tabulka cellpadding="0" cellspacing="0">
		<tr><td colspan="3" height="25" align="left">Po�et osob:</td><td colspan="2">z toho bez poplatku</td></tr>
		<tr><td>dosp�l�:</td><td><input type="text" id="osob_zam" name="osob_zam" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["osob_zam"]?>"></td>
			<td>d�ti:</td><td><input type="text" id="osob_zad" name="osob_zad" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["osob_zad"]?>"></td>
			<td><input type="text" id="inv_zam" name="inv_zam" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["inv_zam"]?>"><input type="hidden" id="inv_zad" name="inv_zad" value="0"></td></tr>
		<tr><td>dosp�l�:</td><td><input type="text" id="osob_ciz" name="osob_ciz" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["osob_ciz"]?>"></td>
			<td>d�ti:</td><td><input type="text" id="osob_cid" name="osob_cid" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["osob_cid"]?>"></td>
			<td><input type="text" id="inv_ciz" name="inv_ciz" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["inv_ciz"]?>"><input type="hidden" id="inv_cid" name="inv_cid" value="0"></td></tr>
		</table>
	</td></tr>
	<tr><td colspan="2" height="5"></td></tr>
</table><br>

<table cellpadding="0" cellspacing="0" width="605">
<tr><td colspan="4" height="8"></td></tr>
<tr><td width="100">Jm�no:</td><td><input type="text" id="jmeno" name="jmeno" size="22" maxlength="20" onchange="top.zmeneno = true;" value="<?php echo $_POST["jmeno"]?>"></td>
	<td>p��jmen�:</td><td><input type="text" id="prijmeni" name="prijmeni" size="30" maxlength="30" onchange="top.zmeneno = true;" value="<?php echo $_POST["prijmeni"]?>"></td></tr>
<tr><td>Bydli�t�:</td><td colspan="3"><input type="text" id="bydliste" name="bydliste" size="65" maxlength="60" onchange="top.zmeneno = true;" value="<?php echo $_POST["bydliste"]?>"></td></tr>
</table>
<table cellpadding="0" cellspacing="0" width="605">
<tr><td width="100">N�stup:</td><td><input type="text" class=cislo id="nastup" name="nastup" size="10" maxlength="10" onchange="prepocti()" value="<?php echo DateEnCz($_POST["nastup"])?>"></td>
	<td>ukon�en�:</td><td><input type="text" class=cislo id="konec" name="konec" size="10" maxlength="10" onchange="prepocti()" value="<?php echo DateEnCz($_POST["konec"])?>"></td>
	<td>pokoj:</td><td><input type="text" id="pokoj" name="pokoj" size="12" maxlength="10" onchange="top.zmeneno = true;" value="<?php echo $_POST["pokoj"]?>"></td><td></td></tr>
	<tr><td colspan="6" height="5"></td></tr>
</table><br>
<table class="tabulka" cellspacing="0" cellpadding="0" width="720">
<tr class="zahlavi">
    <td></td>
    <td style="width:50">po�et osob</td>
    <td style="width:50">po�et noc�</td>
    <td>cena za polo�ku</td>
    <td>DPH</td>
    <td>poplatek z pobytu</td>
    <td>celkem</td>
</tr>
<tr id="zam_j" class="lichy">
    <td align="left">zam�stnanec za jednotku</td>
    <td></td>
    <td></td>
    <td><?php echo $_POST["c_zam_pou"]?> K�</td>
    <td><?php echo $_POST["c_zam_dph"]?> K�</td>
    <td><?php echo $_POST["c_zam_rek"]?> K�</td>
    <td><?php echo soucetRadku("zam");?> K�</td>
</tr>
<tr id="zam_c" class="sudy">
    <td align="left">zam�stnanec</td>
    <td id="t_osob_zam"></td>
    <td id="t_noci_zam"></td>
    <td id="t_pou_zam"></td>
    <td id="t_dph_zam"></td>
    <td id="t_rek_zam"></td>
    <td id="t_cel_zam"></td>
</tr>
<tr id="zad_j" class="lichy">
    <td align="left">d�ti za jednotku</td>
    <td></td>
    <td></td>
    <td><?php echo $_POST["c_zad_pou"]?> K�</td>
    <td><?php echo $_POST["c_zad_dph"]?> K�</td>
    <td><?php echo $_POST["c_zad_rek"]?> K�</td>
    <td><?php echo soucetRadku("zad");?> K�</td>
</tr>
<tr id="zad_c" class="sudy">
    <td align="left">d�ti</td>
    <td id="t_osob_zad"></td>
    <td id="t_noci_zad"></td>
    <td id="t_pou_zad"></td>
    <td id="t_dph_zad"></td>
    <td id="t_rek_zad"></td>
    <td id="t_cel_zad"></td>
</tr>
<tr id="ciz_j" class="lichy">
    <td align="left">ciz� - dop�l� za jednotku</td>
    <td></td>
    <td></td>
    <td><?php echo $_POST["c_ciz_pou"]?> K�</td>
    <td><?php echo $_POST["c_ciz_dph"]?> K�</td>
    <td><?php echo $_POST["c_ciz_rek"]?> K�</td>
    <td><?php echo soucetRadku("ciz");?> K�</td>
</tr>
<tr id="ciz_c" class="sudy">
    <td align="left">ciz� - dosp�l�</td>
    <td id="t_osob_ciz"></td>
    <td id="t_noci_ciz"></td>
    <td id="t_pou_ciz"></td>
    <td id="t_dph_ciz"></td>
    <td id="t_rek_ciz"></td>
    <td id="t_cel_ciz"></td>
</tr>
<tr id="cid_j" class="lichy">
    <td align="left">ciz� - d�ti za jednotku</td>
    <td></td>
    <td></td>
    <td><?php echo $_POST["c_cid_pou"]?> K�</td>
    <td><?php echo $_POST["c_cid_dph"]?> K�</td>
    <td><?php echo $_POST["c_cid_rek"]?> K�</td>
    <td><?php echo soucetRadku("cid");?> K�</td>
</tr>
<tr id="cid_c" class="sudy">
    <td align="left">ciz� - d�ti</td>
    <td id="t_osob_cid"></td>
    <td id="t_noci_cid"></td>
    <td id="t_pou_cid"></td>
    <td id="t_dph_cid"></td>
    <td id="t_rek_cid"></td>
    <td id="t_cel_cid"></td>
</tr>
<tr class="celkem">
    <td align="left" height="20">Celkem:</td>
    <td></td>
    <td></td>
    <td id="t_pou_cel"></td>
    <td id="t_dph_cel"></td>
    <td id="t_rek_cel"></td>
    <td id="t_cel_cel"></td>
</tr>
</table>
<h2>K �hrad�: <span id="kuhrade">0,00</span> K�</h2>
<div id="t_poukaz" align="right" onClick="proved('polozka')">
<input type="hidden" id="akce" name="akce">
<button name="ulozit">Ulo�it</button>
<button name="tisk">Vytisknout</button>
<button id="tarif" name="tarif">Aktualizovat tarif</button>
<button name="stornovat">Stornovat</button>
</div>
Text do poukazu:<br>
<textarea style="width:720" cols="80" rows="5" onchange="top.zmeneno = true;" id="poznamka" name="poznamka"><?php echo $_POST["poznamka"]?></textarea>
</form>
</td></tr></table>
</body>
</html>
