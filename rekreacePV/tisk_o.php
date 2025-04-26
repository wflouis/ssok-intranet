<?php
function celkRadek($radek) {
	$celkem = $_POST["c_".$radek."_pou"]+$_POST["c_".$radek."_dph"]+$_POST["c_".$radek."_rek"];
	return number_format($celkem,2,"."," ");
}
include "funkce/databaze.php"; 
include "funkce/funkce.php"; 
$RIp = vratIP();
if (!maPristup())
	exit;
$neniPole = array("");
akceSDbf("nacti","objekty",$neniPole,"objekt",$_SESSION["objekt"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Tisk rekreačního objektu</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="tisk.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="index.js"></script>
</head>
<body onLoad="tisk.printing.Print(true);">
<h1>Rekreační objekt č. <?php echo $_POST["objekt"];?></h1>
<table cellspacing="0" cellpadding="0">
	<table class=h3 cellspacing="0" cellpadding="0">
	<tr><td>Objekt :</td><td><?php echo $_POST["nazev"];?></td></tr>
	<tr><td></td><td><?php echo $_POST["adresa"];?></td></tr>
	<tr><td></td><td>Tel.: <?php echo $_POST["telefon"];?></td></tr></table><br>

	<h3>Ceník rekreačního poukazu k <?php echo Date("d.m.Y",Time());?>:</h3>
	
	<table class=tabulka cellpadding="0" cellspacing="0">
	<tr class=zahlavi><td></td><td>poukaz</td><td>DPH</td><td>popl.z pobytu</td><td>celkem</td><td>kapacitní popl.</td></tr>
	<tr class=lichy><td align="left">zaměstnanci</td><td><?php echo $_POST["c_zam_pou"]?></td><td><?php echo $_POST["c_zam_dph"]?></td><td><?php echo $_POST["c_zam_rek"]?></td><td id="t_zam_cel"><?php echo celkRadek("zam");?></td><td><?php echo $_POST["c_zam_kap"]?></td></tr>
	<tr class=sudy><td>- děti</td><td><?php echo $_POST["c_zad_pou"]?></td><td><?php echo $_POST["c_zad_dph"]?></td><td><?php echo $_POST["c_zad_rek"]?></td><td id="t_zad_cel"><?php echo celkRadek("zad");?></td><td><?php echo $_POST["c_zad_kap"]?></td></tr>
	<tr class=lichy><td align="left">cizí</td><td><?php echo $_POST["c_ciz_pou"]?></td><td><?php echo $_POST["c_ciz_dph"]?></td><td><?php echo $_POST["c_ciz_rek"]?></td><td id="t_ciz_cel"><?php echo celkRadek("ciz");?></td><td><?php echo $_POST["c_ciz_kap"]?></td></tr>
	<tr class=sudy><td>- děti</td><td><?php echo $_POST["c_cid_pou"]?></td><td><?php echo $_POST["c_cid_dph"]?></td><td><?php echo $_POST["c_cid_rek"]?></td><td id="t_cid_cel"><?php echo celkRadek("cid");?></td><td><?php echo $_POST["c_cid_kap"]?></td></tr>
	</table><br>
	<p>Text do poukazu:</p>
	<textarea id=text style="display:none"><?php echo $_POST["text"];?></textarea>
	<table cellpadding="0" cellspacing="0"><tr><td><span id=pozn1></span><br><br>
	<span id=pozn2></span></td><td><?php if (!empty($_POST["obrazky"])) echo "<img src=\"obrazky/".$_POST["obrazky"]."\" alt=\"\">";?></td></tr></table>
	<script>
		getObj("pozn1").innerText = getObj("text").value;
	</script>
	<object id=tisk style="display:none"
	  classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814"
  	  codebase="http://www.su-prostejov.cz/rekreace/ScriptX.cab#Version=6,2,433,14">
	</object>
	<script>
			tisk.printing.header = "";
			tisk.printing.footer = "";
			tisk.printing.portrait = true;
			tisk.printing.leftMargin = 0.0;
			tisk.printing.topMargin = 0.0;
			tisk.printing.rightMargin = 0.0;
			tisk.printing.bottomMargin = 0.0;
	</script>
</table>	
</html>
