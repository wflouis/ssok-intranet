<?php
function soucetRadku($radek) {
	$celkem = $_POST["c_".$radek."_pou"]+$_POST["c_".$radek."_dph"]+$_POST["c_".$radek."_rek"];
	return number_format($celkem,2,"."," ");
}
function celkRadek($radek) {
	global $noci;
	$celkem = round($noci*($_POST["osob_".$radek]*($_POST["c_".$radek."_pou"]+$_POST["c_".$radek."_dph"])+($_POST["osob_".$radek]-$_POST["inv_".$radek])*$_POST["c_".$radek."_rek"]));
	return number_format($celkem,2,"."," ");
}
function celkSloupec($sloupec) {
	global $noci;
	if ($sloupec == 'rek')
		$celkem = round($noci*($_POST["osob_zam"]-$_POST["inv_zam"])*$_POST["c_zam_".$sloupec])+
				  round($noci*$_POST["osob_zad"]*$_POST["c_zad_".$sloupec])+
				  round($noci*($_POST["osob_ciz"]-$_POST["inv_ciz"])*$_POST["c_ciz_".$sloupec])+
				  round($noci*$_POST["osob_cid"]*$_POST["c_cid_".$sloupec]);
	else
		$celkem = round($noci*$_POST["osob_zam"]*$_POST["c_zam_".$sloupec])+
				  round($noci*$_POST["osob_zad"]*$_POST["c_zad_".$sloupec])+
				  round($noci*$_POST["osob_ciz"]*$_POST["c_ciz_".$sloupec])+
				  round($noci*$_POST["osob_cid"]*$_POST["c_cid_".$sloupec]);
	return number_format($celkem,2,"."," ");
}
function kUhrade() {
	global $noci;
	$celkem = round($noci*($_POST["osob_zam"]*($_POST["c_zam_pou"]+$_POST["c_zam_dph"])+($_POST["osob_zam"]-$_POST["inv_zam"])*$_POST["c_zam_rek"]))+
		 	  round($noci*$_POST["osob_zad"]*($_POST["c_zad_pou"]+$_POST["c_zad_dph"]+$_POST["c_zad_rek"]))+
			  round($noci*($_POST["osob_ciz"]*($_POST["c_ciz_pou"]+$_POST["c_ciz_dph"])+($_POST["osob_ciz"]-$_POST["inv_ciz"])*$_POST["c_ciz_rek"]))+
			  round($noci*$_POST["osob_cid"]*($_POST["c_cid_pou"]+$_POST["c_cid_dph"]+$_POST["c_cid_rek"]));
	return number_format($celkem,2,"."," ");
}
function skryjRadek($index) {
	if ($_POST["osob_".$index]>0) {
		echo "getObj(\"".$index."_c\").style.display = \"table-row\";\n";
		echo "getObj(\"".$index."_j\").style.display = \"table-row\";\n";
	} else {
		echo "getObj(\"".$index."_c\").style.display = \"table-row\";\n";
		echo "getObj(\"".$index."_j\").style.display = \"table-row\";\n";
	}
}
include "funkce/databaze.php"; 
include "funkce/funkce.php"; 

$_POST["inv_zad"] = 0;
$_POST["inv_cid"] = 0;
$RIp = vratIP();
if (!maPristup()) 
	exit;
$neniPole = array("");
akceSDbf("nacti","objekty",$neniPole,"objekt",$_SESSION["objekt"]);
akceSDbf("nacti","poukazy",$neniPole,"poukaz",$_SESSION["poukaz"],"objekt",$_SESSION["objekt"],"rok",$_SESSION["rok"]);
$noci = round((strtotime($_POST["konec"])-strtotime($_POST["nastup"]))/86400);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Tisk rekrea�n�ho poukazu</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="tisk.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="index.js"></script>
</head>
<body onLoad="tisk.printing.Print(true);" leftmargin="20">
<h1>Rekrea�n� poukaz �. <?php echo $_POST["zkratka"].$_POST["rok"].str_pad($_POST["poukaz"], 3, "0", STR_PAD_LEFT);?></h2>
<table cellspacing="0" cellpadding="0">
	<table class=h3 cellspacing="0" cellpadding="0">
	<tr><td>Objekt :</td><td><?php echo $_POST["nazev"];?></td></tr>
	<tr><td></td><td><?php echo $_POST["adresa"];?></td></tr>
	<tr><td></td><td>Tel.: <?php echo $_POST["telefon"];?></td></tr></table><br>
	<table cellpadding="0" cellspacing="0">
		<tr><td colspan="2">&nbsp;</td>
			<td colspan="4" align="left">Po�et osob:</td><td colspan="2">z toho bez poplatku</td></tr>
		<tr><td width="100">Zam�stnanec:</td>
			<td><?php echo $_POST["nazev_zam"];?></td>
			<td>dosp�l�:</td><td><?php echo $_POST["osob_zam"];?></td>
			<td>d�ti:</td><td><?php echo $_POST["osob_zad"];?></td>
			<td align="right"><?php echo $_POST["inv_zam"];?></td></tr>
		<tr><td>Ciz�:</td>
			<td><?php echo $_POST["nazev_ciz"];?></td>
			<td>dosp�l�:</td><td><?php echo $_POST["osob_ciz"];?></td>
			<td>d�ti:</td><td><?php echo $_POST["osob_cid"];?></td>
			<td align="right"><?php echo $_POST["inv_ciz"];?></td></tr>
	</table>
	
	<table cellpadding="0" cellspacing="0" width="605">
	<tr><td colspan="4" height="8"></td></tr>
	<tr><td width="100">Jm�no:</td><td style="font-weight : bold;"><?php echo $_POST["jmeno"]." ".$_POST["prijmeni"];?></td>
		<td></td><td></td></tr>
	<tr><td>Bydli�t�:</td><td colspan="3"><?php echo $_POST["bydliste"];?></td></tr>
	</table>
	<table cellpadding="0" cellspacing="0" width="605">
	<tr><td width="100">N�stup:</td><td><?php echo DateEnCz($_POST["nastup"]);?></td>
		<td>ukon�en�:</td><td><?php echo DateEnCz($_POST["konec"]);?></td>
		<td>pokoj:</td><td><?php echo $_POST["pokoj"];?></td><td></td></tr>
		<tr><td colspan="6" height="5"></td></tr>
	</table><br>
	<table class=tabulka cellspacing="0" cellpadding="0" width="720">
	<tr class=zahlavi>
	    <td></td>
	    <td style="width:50">po�et osob</td>
	    <td style="width:50">po�et noc�</td>
	    <td>cena za polo�ku</td>
	    <td>DPH</td>
	    <td>poplatek z pobytu</td>
	    <td>celkem</td>
	</tr>
	<tr id=zam_j class=lichy>
	    <td align="left">zam�stnanec za jednotku</td>
	    <td></td>
	    <td></td>
	    <td><?php echo $_POST["c_zam_pou"];?> K�</td>
	    <td><?php echo $_POST["c_zam_dph"];?> K�</td>
	    <td><?php echo $_POST["c_zam_rek"];?> K�</td>
	    <td><?php echo soucetRadku("zam");?> K�</td>
	</tr>
	<tr id=zam_c class=sudy>
	    <td align="left">zam�stnanec</td>
	    <td><?php echo $_POST["osob_zam"];?></td>
	    <td><?php echo ($_POST["osob_zam"]>0?$noci:0);?></td>
	    <td><?php echo number_format($noci*$_POST["osob_zam"]*$_POST["c_zam_pou"],2,"."," ");?> K�</td>
	    <td><?php echo number_format(round($noci*$_POST["osob_zam"]*$_POST["c_zam_dph"]),2,"."," ");?> K�</td>
	    <td><?php echo number_format($noci*($_POST["osob_zam"]-$_POST["inv_zam"])*$_POST["c_zam_rek"],2,"."," ");?> K�</td>
	    <td><?php echo celkRadek("zam");?> K�</td>
	</tr>
	<tr id=zad_j class=lichy>
	    <td align="left">d�ti za jednotku</td>
	    <td></td>
	    <td></td>
	    <td><?php echo $_POST["c_zad_pou"];?> K�</td>
	    <td><?php echo $_POST["c_zad_dph"];?> K�</td>
	    <td><?php echo $_POST["c_zad_rek"];?> K�</td>
	    <td><?php echo soucetRadku("zad");?> K�</td>
	</tr>
	<tr id=zad_c class=sudy>
	    <td align="left">d�ti</td>
	    <td><?php echo $_POST["osob_zad"];?></td>
	    <td><?php echo ($_POST["osob_zad"]>0?$noci:0);?></td>
	    <td><?php echo number_format($noci*$_POST["osob_zad"]*$_POST["c_zad_pou"],2,"."," ");?> K�</td>
	    <td><?php echo number_format(round($noci*$_POST["osob_zad"]*$_POST["c_zad_dph"]),2,"."," ");?> K�</td>
	    <td><?php echo number_format($noci*$_POST["osob_zad"]*$_POST["c_zad_rek"],2,"."," ");?> K�</td>
	    <td><?php echo celkRadek("zad");?> K�</td>
	</tr>
	<tr id=ciz_j class=lichy>
	    <td align="left">ciz� - dop�l� za jednotku</td>
	    <td></td>
	    <td></td>
	    <td><?php echo $_POST["c_ciz_pou"];?> K�</td>
	    <td><?php echo $_POST["c_ciz_dph"];?> K�</td>
	    <td><?php echo $_POST["c_ciz_rek"];?> K�</td>
	    <td><?php echo soucetRadku("ciz");?> K�</td>
	</tr>
	<tr id=ciz_c class=sudy>
	    <td align="left">ciz� - dosp�l�</td>
	    <td><?php echo $_POST["osob_ciz"];?></td>
	    <td><?php echo ($_POST["osob_ciz"]>0?$noci:0);?></td>
	    <td><?php echo number_format($noci*$_POST["osob_ciz"]*$_POST["c_ciz_pou"],2,"."," ");?> K�</td>
	    <td><?php echo number_format(round($noci*$_POST["osob_ciz"]*$_POST["c_ciz_dph"]),2,"."," ");?> K�</td>
	    <td><?php echo number_format($noci*($_POST["osob_ciz"]-$_POST["inv_ciz"])*$_POST["c_ciz_rek"],2,"."," ");?> K�</td>
	    <td><?php echo celkRadek("ciz");?> K�</td>
	</tr>
	<tr id=cid_j class=lichy>
	    <td align="left">ciz� - d�ti za jednotku</td>
	    <td></td>
	    <td></td>
	    <td><?php echo $_POST["c_cid_pou"];?> K�</td>
	    <td><?php echo $_POST["c_cid_dph"];?> K�</td>
	    <td><?php echo $_POST["c_cid_rek"];?> K�</td>
	    <td><?php echo soucetRadku("cid");?> K�</td>
	</tr>
	<tr id=cid_c class=sudy>
	    <td align="left">ciz� - d�ti</td>
	    <td><?php echo $_POST["osob_cid"];?></td>
	    <td><?php echo ($_POST["osob_cid"]>0?$noci:0);?></td>
	    <td><?php echo number_format($noci*$_POST["osob_cid"]*$_POST["c_cid_pou"],2,"."," ");?> K�</td>
	    <td><?php echo number_format(round($noci*$_POST["osob_cid"]*$_POST["c_cid_dph"]),2,"."," ");?> K�</td>
	    <td><?php echo number_format($noci*$_POST["osob_cid"]*$_POST["c_cid_rek"],2,"."," ");?> K�</td>
	    <td><?php echo celkRadek("cid");?> K�</td>
	</tr>
	<tr class=celkem>
	    <td align="left" height="20">Celkem:</td>
	    <td></td>
	    <td></td>
	    <td><?php echo celkSloupec("pou");?> K�</td>
	    <td><?php echo celkSloupec("dph");?> K�</td>
	    <td><?php echo celkSloupec("rek");?> K�</td>
	    <td><?php echo kUhrade();?> K�</td>
	</tr>
	</table>
	<h3>K �hrad�: <?php echo kUhrade();?> K�</h3>
	<textarea id=text style="display:none"><?php echo $_POST["text"];?></textarea>
	<textarea id=poznamka style="display:none"><?php echo $_POST["poznamka"];?></textarea>
	<table cellpadding="0" cellspacing="0"><tr><td><span id=pozn1></span><br><br>
	<span id=pozn2></span></td><td><?php if (!empty($_POST["obrazky"])) echo "<img src=\"obrazky/".$_POST["obrazky"]."\" alt=\"\">";?></td></tr></table>
	<script>
		getObj("pozn1").innerText = getObj("text").value;
		getObj("pozn2").innerText = getObj("poznamka").value;
		// skr�t nulov� ��dky
		<?php
			skryjRadek("zam");
			skryjRadek("zad");
			skryjRadek("ciz");
			skryjRadek("cid");
		?>
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
