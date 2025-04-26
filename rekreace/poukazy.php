<?php
function celkCena($zaznam) {
	$noci = (strtotime($zaznam["konec"])-strtotime($zaznam["nastup"]))/86400;
	$celkem = 
	$zaznam["osob_zam"]*($zaznam["c_zam_pou"]+$zaznam["c_zam_dph"])+
	($zaznam["osob_zam"]-$zaznam["inv_zam"])*$zaznam["c_zam_rek"]+
	$zaznam["osob_zad"]*($zaznam["c_zad_pou"]+$zaznam["c_zad_dph"]+$zaznam["c_zad_rek"])+
	$zaznam["osob_ciz"]*($zaznam["c_ciz_pou"]+$zaznam["c_ciz_dph"])+
	($zaznam["osob_ciz"]-$zaznam["inv_ciz"])*$zaznam["c_ciz_rek"]+
	$zaznam["osob_cid"]*($zaznam["c_cid_pou"]+$zaznam["c_cid_dph"]+$zaznam["c_cid_rek"]);
	$nasobit = ($zaznam["typCeny"]==0)?$noci:1;
	return $celkem*$nasobit;
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
		if (empty($_POST["objekt"]))
			$_POST["akce"] = "";
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
if (empty($_POST["rok"]))
	$_POST["rok"] = $_SESSION["rok"];
$_POST["zkratka"] = $_SESSION["zkratka"];
if ($_POST["akce"]=="stornovat")
	$_POST["storno"] = !$_SESSION["storno"];

$neniPole = array("akce","typCeny","n_objektu","ulozit","tarif","tisk","stornovat","smazat","serad","poukaz","sloupec","strana","inv_zad","inv_cid");
//echo "Parametry: ",$_POST["akce"],",","poukazy",",","objekt",",",$_POST["objekt"],",","rok",",",$_POST["rok"],",","poukaz",",",$_POST["poukaz"];
akceSDbf($_POST["akce"],"poukazy",$neniPole,"poukaz",$_POST["poukaz"],"objekt",$_POST["objekt"],"rok",$_POST["rok"]);
$result = mysql_query("SELECT typCeny FROM objekty WHERE objekt='".$_POST["objekt"]."' LIMIT 1");
if ($zaznam = mysql_fetch_assoc($result))
	$_POST["typCeny"] = $zaznam["typCeny"]; 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
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
?>
		}
</script>
</head>
<body 
<?php  $zalozka = array("nacti","novy","tarif","ulozit");
	if (in_array($_POST["akce"],$zalozka))
		echo "onload=\"prepni('o2');"; 
	else 
		echo "onload=\"prepni('o1');"; 
	echo " popisy(".$_POST["typCeny"].");\"";
?>> 
<h3 id=nadpisS style="display: none;">Seznam rekreaèních poukazù</h3>
<h3 id=nadpisP style="display: none;">Rekreaèní poukaz è. <?php echo $_POST["zkratka"].$_POST["rok"].str_pad($_POST["poukaz"], 3, "0", STR_PAD_LEFT);?></h3>
<table cellpadding="0" cellspacing="0"><tr><td>
<?php
$result = mysql_query("SELECT count(poukaz) as pocet FROM poukazy WHERE 1 = 1");
$zaznam = mysql_fetch_assoc($result);
$posledni = ceil($zaznam["pocet"]/pocRadku);
switch ($_POST["akce"]) {
	case "zacatek": $_POST["strana"] = 1; break;
	case "vlevo": $_POST["strana"] = ceil($_POST["strana"]/pocStran)*pocStran-pocStran; break;
	case "vpravo": $_POST["strana"] = ceil($_POST["strana"]/pocStran)*pocStran+1; break;
	case "konec": $_POST["strana"] = $posledni; break;
}
$_SESSION["strana"] = $_POST["strana"];
?>
<table id=posun align="right"><tr><td onClick="navigace()">
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
<table class=ouska cellpadding="0" cellspacing="0" width="160" onClick="prepni(event.srcElement.id);"><tr align="center" style="font-weight : bold;"><td id=o1 class=pasOusko height="30">Seznam</td><td id=o2 class=pasOusko>Poukaz</td></tr></table>
<form action="poukazy.php" method="post" name="vyber" id="vyber" style="margin: 0; display : none;">
<table cellpadding="1" cellspacing="1" class=tabulka onMouseOver="rozsvit()" onMouseOut="zhasni()" onClick="nacti('poukaz')">
<tr class=zahlavi style="text-align : left;"><td>Poukaz <?php echo razeni("rok DESC,poukaz");?></td><td>od <?php echo razeni("nastup");?></td><td>do <?php echo razeni("konec");?></td><td style="width:180;">jméno <?php echo razeni("prijmeni");?></td><td>osob (zam/cizí)</td><td style="width:85">cena</td></tr>
<?php 	
$result = mysql_query("SELECT poukazy.*, typCeny FROM poukazy join objekty on poukazy.objekt=objekty.objekt WHERE 1=1 ORDER BY ".$_POST["sloupec"].$_SESSION["smer"]." LIMIT ".(($_POST["strana"]-1)*pocRadku).",".pocRadku);
$Pocet = mysql_num_rows($result);
if ($Pocet > 0) {
	for($i=1; $i<=$Pocet; $i++):
		$zaznam = mysql_fetch_assoc($result);
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
		echo "<td>".($zaznam["osob_zam"]+$zaznam["osob_zad"])."/".($zaznam["osob_ciz"]+$zaznam["osob_cid"])."</td>\n";
		if ($zaznam["celkem"]>0)
			echo "<td>".number_format($zaznam["celkem"],2,"."," ")." Kè</td></tr>\n";
		else
			echo "<td>".number_format(celkCena($zaznam),2,"."," ")." Kè</td></tr>\n";
	endfor;
}	
?>
</table><br>
<div align="right" onClick="proved('vyber')">
<input type="hidden" name="sloupec">
<input type="hidden" name="strana">
<input type="hidden" name="akce">
<button name="novy">Nový</button>
</div>
</form>

<form action="poukazy.php" method="post" name="polozka" id="polozka" onKeyDown="enter()" onKeyPress="return cisla('.')" style="margin: 0; display : none;">
<table cellpadding="0" cellspacing="0">
	<tr><td colspan="2" height="8"></td></tr>
	<tr><td>
		<table cellpadding="0" cellspacing="0">
			<tr><td>Objekt:</td><td>
			<select name="n_objektu" style="width:307" onchange="getObj('objekt').value = getObj('n_objektu').options(getObj('n_objektu').value-1).id; getObj('tarif').click()" <?php if (!empty($_POST["poukaz"])) echo "disabled"; ?>>
			<?php	$Puvodni = $_POST["storno"];
				$Sql  = "SELECT * FROM objekty WHERE storno = 0 ORDER by objekt";
				$result = mysql_query($Sql);
				$Pocet = mysql_num_rows($result);
				for($i=1; $i<=$Pocet; $i++) {
					$zaznam = mysql_fetch_assoc($result);
					echo "<option id=\"".$zaznam["objekt"]."\" value=\"$i\">".$zaznam["nazev"]."</option>\n";
				}
				$_POST["storno"] = $Puvodni;
				if (!empty($_POST["objekt"])) echo "<script>getObj('n_objektu').value = getObj('".$_POST["objekt"]."').value</script>\n"; ?>
			</select>
			<input type="hidden" name="poukaz" value="<?php echo $_POST["poukaz"]?>">
			<input type="hidden" name="typCeny" value="<?php echo $_POST["typCeny"]?>">
			<input type="hidden" name="objekt" value="<?php echo $_POST["objekt"]?>"></td></tr>
			<input type="hidden" name="celkem" value="<?php echo $_POST["celkem"]?>">
			<tr><td width="100">Zamìstnanec:</td><td><input type="text" name="nazev_zam" size="42" maxlength="40" onchange="top.zmeneno = true; prepocti();" value="<?php echo $_POST["nazev_zam"]?>"></td></tr>
			<tr><td>Cizí:</td><td><input type="text" name="nazev_ciz" size="42" maxlength="40" onchange="top.zmeneno = true; prepocti();" value="<?php echo $_POST["nazev_ciz"]?>"></td></tr>
		</table>
	</td>
	<td>
		<table class="tabulka" cellpadding="0" cellspacing="0">
		<tr><td colspan="3" height="25" align="left">Poèet osob:</td><td colspan="2">z toho bez poplatku</td></tr>
		<tr><td>dospìlí:</td><td><input type="text" name="osob_zam" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["osob_zam"]?>"></td>
			<td>dìti:</td><td><input type="text" name="osob_zad" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["osob_zad"]?>"></td>
			<td><input type="text" name="inv_zam" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["inv_zam"]?>"><input type="hidden" name="inv_zad" value="0"></td></tr>
		<tr><td>dospìlí:</td><td><input type="text" name="osob_ciz" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["osob_ciz"]?>"></td>
			<td>dìti:</td><td><input type="text" name="osob_cid" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["osob_cid"]?>"></td>
			<td><input type="text" name="inv_ciz" size="3" maxlength="2" onchange="prepocti()" value="<?php echo $_POST["inv_ciz"]?>"><input type="hidden" name="inv_cid" value="0"></td></tr>
		</table>
	</td></tr>
	<tr><td colspan="2" height="5"></td></tr>
</table><br>

<table cellpadding="0" cellspacing="0" width="605">
<tr><td colspan="4" height="8"></td></tr>
<tr><td width="100">Jméno:</td><td><input type="text" name="jmeno" size="22" maxlength="20" onchange="top.zmeneno = true;" value="<?php echo $_POST["jmeno"]?>"></td>
	<td>pøíjmení:</td><td><input type="text" name="prijmeni" size="30" maxlength="30" onchange="top.zmeneno = true;" value="<?php echo $_POST["prijmeni"]?>"></td></tr>
<tr><td>Bydlištì:</td><td colspan="3"><input type="text" name="bydliste" size="65" maxlength="60" onchange="top.zmeneno = true;" value="<?php echo $_POST["bydliste"]?>"></td></tr>
</table>
<table cellpadding="0" cellspacing="0" width="605">
<tr><td width="100">Nástup:</td><td><input type="text" class=cislo name="nastup" size="10" maxlength="10" onchange="prepocti()" value="<?php echo DateEnCz($_POST["nastup"])?>"></td>
	<td>ukonèení:</td><td><input type="text" class=cislo name="konec" size="10" maxlength="10" onchange="prepocti()" value="<?php echo DateEnCz($_POST["konec"])?>"></td>
	<td class="popis1">pokoj:</td><td class="popis2">chata:</td><td><input type="text" name="pokoj" size="12" maxlength="10" onchange="top.zmeneno = true;" value="<?php echo $_POST["pokoj"]?>"></td><td></td></tr>
	<tr><td colspan="6" height="5"></td></tr>
</table><br>
<table class=tabulka cellspacing="0" cellpadding="0" width="720">
<tr class=zahlavi>
    <td></td>
    <td style="width:50" class="popis1 popis">poèet osob</td><td style="width:50" class="popis2 popis">poèet</td>
    <td style="width:50">poèet nocí</td>
    <td>cena za položku</td>
    <td>DPH</td>
    <td>ubytovací poplatek</td>
    <td>celkem</td>
</tr>
<tr id=zam_j class="popis1 lichy">
    <td align="left">zamìstnanec za jednotku</td>
    <td></td>
    <td></td>
    <td><?php echo $_POST["c_zam_pou"]?> Kè</td>
    <td><?php echo $_POST["c_zam_dph"]?> Kè</td>
    <td><?php echo $_POST["c_zam_rek"]?> Kè</td>
    <td><?php echo soucetRadku("zam");?> Kè</td>
</tr>
<tr id=zam_c class=sudy>
    <td align="left" class="popis1">zamìstnanec</td><td align="left" class="popis2">zamìstnanec - víkend</td>
    <td id=t_osob_zam></td>
    <td id=t_noci_zam></td>
    <td id=t_pou_zam></td>
    <td id=t_dph_zam></td>
    <td id=t_rek_zam></td>
    <td id=t_cel_zam></td>
</tr>
<tr id=zad_j class="popis1 lichy">
    <td align="left">dìti za jednotku</td>
    <td></td>
    <td></td>
    <td><?php echo $_POST["c_zad_pou"]?> Kè</td>
    <td><?php echo $_POST["c_zad_dph"]?> Kè</td>
    <td><?php echo $_POST["c_zad_rek"]?> Kè</td>
    <td><?php echo soucetRadku("zad");?> Kè</td>
</tr>
<tr id=zad_c class=sudy>
    <td align="left" class="popis1">dìti</td><td align="left" class="popis2">cizí - víkend</td>
    <td id=t_osob_zad></td>
    <td id=t_noci_zad></td>
    <td id=t_pou_zad></td>
    <td id=t_dph_zad></td>
    <td id=t_rek_zad></td>
    <td id=t_cel_zad></td>
</tr>
<tr id=ciz_j class="popis1 lichy">
    <td align="left">cizí - dopìlí za jednotku</td>
    <td></td>
    <td></td>
    <td><?php echo $_POST["c_ciz_pou"]?> Kè</td>
    <td><?php echo $_POST["c_ciz_dph"]?> Kè</td>
    <td><?php echo $_POST["c_ciz_rek"]?> Kè</td>
    <td><?php echo soucetRadku("ciz");?> Kè</td>
</tr>
<tr id=ciz_c class=sudy>
    <td align="left" class="popis1">cizí - dospìlí</td><td align="left" class="popis2">zamìstnanec - týden</td>
    <td id=t_osob_ciz></td>
    <td id=t_noci_ciz></td>
    <td id=t_pou_ciz></td>
    <td id=t_dph_ciz></td>
    <td id=t_rek_ciz></td>
    <td id=t_cel_ciz></td>
</tr>
<tr id=cid_j class="popis1 lichy">
    <td align="left">cizí - dìti za jednotku</td>
    <td></td>
    <td></td>
    <td><?php echo $_POST["c_cid_pou"]?> Kè</td>
    <td><?php echo $_POST["c_cid_dph"]?> Kè</td>
    <td><?php echo $_POST["c_cid_rek"]?> Kè</td>
    <td><?php echo soucetRadku("cid");?> Kè</td>
</tr>
<tr id=cid_c class=sudy>
    <td align="left" class="popis1">cizí - dìti</td><td align="left" class="popis2">cizí - týden</td>
    <td id=t_osob_cid></td>
    <td id=t_noci_cid></td>
    <td id=t_pou_cid></td>
    <td id=t_dph_cid></td>
    <td id=t_rek_cid></td>
    <td id=t_cel_cid></td>
</tr>
<tr class=celkem>
    <td align="left" height="20">Celkem:</td>
    <td></td>
    <td></td>
    <td id=t_pou_cel></td>
    <td id=t_dph_cel></td>
    <td id=t_rek_cel></td>
    <td id=t_cel_cel></td>
</tr>
</table>
<h2>K úhradì: <span id=kuhrade>0,00</span> Kè</h2>
<div id=t_poukaz align="right" onClick="proved('polozka')">
<input type="hidden" name="akce">
<button name="ulozit">Uložit</button>
<button name="tisk">Vytisknout</button>
<button name="tarif">Aktualizovat tarif</button>
<button name="stornovat">Stornovat</button>
</div>
Text do poukazu:<br>
<textarea style="width:720" cols="80" rows="5" onchange="top.zmeneno = true;" id=poznamka name="poznamka"><?php echo $_POST["poznamka"]?></textarea>
</form>
</td></tr></table>
</body>
</html>
