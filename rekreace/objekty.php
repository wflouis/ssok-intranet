<?php
include "funkce/databaze.php"; 
include "funkce/funkce.php"; 
$RIp = vratIP();
if (!maPristup())
	exit;

if (empty($_POST["serad"])) {
	$_POST["serad"] = "objekt";
}
if (!isset($_POST["typCeny"]))
	$_POST["typCeny"] = 0;
	
if ($_POST["akce"]=="stornovat")
	$_POST["storno"] = !$_SESSION["storno"];

$neniPole = array("akce","ulozit","tisk","smazat","serad","objekt","stornovat");
akceSDbf($_POST["akce"],"objekty",$neniPole,"objekt",$_POST["objekt"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="index.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="index.js"></script>
	<script language="JavaScript" src="objekt.js"></script>
</head>
<body <?php echo ($_POST["akce"] == "nacti" || $_POST["akce"] == "novy")? "onload=\"prepni('o2');":"onload=\"prepni('o1');"; echo " popisy(".$_POST["typCeny"].");\""; ?>>
<h3 id=nadpis style="padding-left : 5;"></h3>
<table cellpadding="0" cellspacing="0"><tr><td>
<table id=posun><tr><td></td></tr></table>
<table class=ouska cellpadding="0" cellspacing="0" width="160" onClick="prepni(event.srcElement.id);"><tr align="center" style="font-weight : bold;"><td id=o1 class=pasOusko height="30">Seznam</td><td id=o2 class=pasOusko>Objekt</td></tr></table>
<form action="objekty.php" method="post" name="vyber" id="vyber" style="margin: 0; display : none;">
<table cellpadding="1" cellspacing="1" onMouseOver="rozsvit()" onMouseOut="zhasni()" onClick="nacti('objekt')">
<tr class=zahlavi><td>Číslo</td><td></td><td style="width:300">název objektu</td></tr>
<?php 	
$_SESSION["objekt"] = $_POST["objekt"];	//pro tisk;
if (empty($_POST["strana"]))
	$_POST["strana"] = 1;	
do {	
	$result = mysql_query("SELECT * FROM objekty WHERE 1=1 ORDER BY ".$_POST["serad"]." LIMIT ".(($_POST["strana"]-1)*20).",20");
	$Pocet = mysql_num_rows($result);
	if ($Pocet == 0 && $_POST["strana"] > 1) {
		$result = mysql_query("SELECT count(objekt) as pocet FROM objekty WHERE 1 = 1");
		if ($result) {
			$zaznam = mysql_fetch_assoc($result);
			$_POST["strana"] = ceil($zaznam["pocet"]/20);
		} else
			$_POST["strana"] = 1;
	}
} while ($Pocet == 0 && $_POST["strana"] > 1);
if ($Pocet > 0) {
	for($i=1; $i<=$Pocet; $i++):
		$zaznam = mysql_fetch_assoc($result);
		if ($i%2 == 0)
			echo "<tr class=\"sudy";
		else	
			echo "<tr class=\"lichy";
		if ($zaznam["storno"]) echo " storno";
		if ($zaznam["objekt"]==$_POST["objekt"]) echo " vybrany";
		echo "\" id=\"".$zaznam["objekt"]."\">\n<td>".$zaznam["objekt"]."</td><td></td>\n";
		echo "<td>".$zaznam["nazev"]."</td></tr>\n";
	endfor;
}	
?>
</table><br>
<div align="right" onClick="proved('vyber')">
<input type="hidden" name="akce">
<button name="novy">Nový</button>
</div>
</form>

<form action="objekty.php" method="post" name="polozka" id="polozka" onKeyDown="enter()" onKeyPress="return cisla('. ')" style="margin: 0; display : none;">
<table cellpadding="0" cellspacing="0">
<tr><td colspan="4" height="8"></td></tr>
<tr><td>Název:</td><td><input type="text" name="nazev" size="45" maxlength="40" onchange="top.zmeneno = true;" value="<?php echo $_POST["nazev"]?>"></td>
	<td>Zkratka:</td><td><input type="text" name="zkratka" size="2" maxlength="2" onchange="top.zmeneno = true;" value="<?php echo $_POST["zkratka"]?>"></td></tr>
<tr><td>Adresa:</td><td colspan="3"><input type="text" name="adresa" size="55" maxlength="50" onchange="top.zmeneno = true;" value="<?php echo $_POST["adresa"]?>"></td></tr>
<tr><td>Telefon:</td><td colspan="3"><input class=cislo type="text" name="telefon" size="12" maxlength="11" onchange="top.zmeneno = true;" value="<?php echo $_POST["telefon"]?>"></td></tr>
<tr><td colspan="4" height="5"></td></tr>
</table><br>
<p><strong>Ceník rekreačního poukazu:</strong></p>
<p><input type="radio" name="typCeny" value="0" onClick="popisy(this.value);" <?php echo ($_POST["typCeny"]==0)?"checked":""; ?>>za osobu a noc <input type="radio" name="typCeny" value="1" onClick="popisy(this.value);" <?php echo ($_POST["typCeny"]==1)?"checked":""; ?>>za týden/víkend</p>
<table class=tabulka cellpadding="0" cellspacing="0">
<tr class=zahlavi><td></td><td>poukaz</td><td>DPH</td><td>ubytovací popl.</td><td>celkem</td><td>kapacitní popl.</td></tr>
<tr class=lichy><td align="left" class="popis1">zaměstnanci</td><td class="popis2">zaměstnanci - víkend</td><td><input type="text" name="c_zam_pou" onchange="prepocti()" value="<?php echo $_POST["c_zam_pou"]?>" size="6" maxlength="6"></td><td><input type="text" name="c_zam_dph" onchange="prepocti()" value="<?php echo $_POST["c_zam_dph"]?>" size="6" maxlength="6"></td><td><input type="text" name="c_zam_rek" onchange="prepocti()" value="<?php echo $_POST["c_zam_rek"]?>" size="6" maxlength="6"></td><td id="t_zam_cel">0.00</td><td><input type="text" name="c_zam_kap" onchange="prepocti()" value="<?php echo $_POST["c_zam_kap"]?>" size="6" maxlength="6"></td></tr>
<tr class=sudy><td class="popis1">- děti</td><td class="popis2">cizí - víkend</td><td><input type="text" name="c_zad_pou" onchange="prepocti()" value="<?php echo $_POST["c_zad_pou"]?>" size="6" maxlength="6"></td><td><input type="text" name="c_zad_dph" onchange="prepocti()" value="<?php echo $_POST["c_zad_dph"]?>" size="6" maxlength="6"></td><td><input type="text" name="c_zad_rek" onchange="prepocti()" value="<?php echo $_POST["c_zad_rek"]?>" size="6" maxlength="6"></td><td id="t_zad_cel">0.00</td><td><input type="text" name="c_zad_kap" onchange="prepocti()" value="<?php echo $_POST["c_zad_kap"]?>" size="6" maxlength="6"></td></tr>
<tr class=lichy><td align="left" class="popis1">cizí</td><td class="popis2">zaměstnanci - týden</td><td><input type="text" name="c_ciz_pou" onchange="prepocti()" value="<?php echo $_POST["c_ciz_pou"]?>" size="6" maxlength="6"></td><td><input type="text" name="c_ciz_dph" onchange="prepocti()" value="<?php echo $_POST["c_ciz_dph"]?>" size="6" maxlength="6"></td><td><input type="text" name="c_ciz_rek" onchange="prepocti()" value="<?php echo $_POST["c_ciz_rek"]?>" size="6" maxlength="6"></td><td id="t_ciz_cel">0.00</td><td><input type="text" name="c_ciz_kap" onchange="prepocti()" value="<?php echo $_POST["c_ciz_kap"]?>" size="6" maxlength="6"></td></tr>
<tr class=sudy><td  class="popis1">- děti</td><td class="popis2">cizí - týden</td><td><input type="text" name="c_cid_pou" onchange="prepocti()" value="<?php echo $_POST["c_cid_pou"]?>" size="6" maxlength="6"></td><td><input type="text" name="c_cid_dph" onchange="prepocti()" value="<?php echo $_POST["c_cid_dph"]?>" size="6" maxlength="6"></td><td><input type="text" name="c_cid_rek" onchange="prepocti()" value="<?php echo $_POST["c_cid_rek"]?>" size="6" maxlength="6"></td><td id="t_cid_cel">0.00</td><td><input type="text" name="c_cid_kap" onchange="prepocti()" value="<?php echo $_POST["c_cid_kap"]?>" size="6" maxlength="6"></td></tr>
</table><br>
<div id=t_objekt align="right" onClick="proved('polozka')">
<input type="hidden" name="objekt" value="<?php echo $_POST["objekt"]?>">
<input type="hidden" name="akce">
<button name="ulozit">Uložit</button>
<button name="tisk">Vytisknout</button>
<button name="stornovat">Stornovat</button>
</div>
Text do poukazu:<br>
<textarea cols="85" rows="20" id=text onchange="top.zmeneno = true;" name="text"><?php echo $_POST["text"]?></textarea>
</form>
</td></tr></table>
</body>
</html>
