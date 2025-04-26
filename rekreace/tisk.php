<?php
function soucetRadku($radek) {
	return number_format($_POST["c_".$radek."_pou"]+$_POST["c_".$radek."_dph"]+$_POST["c_".$radek."_rek"],2,"."," ");
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
$result = mysql_query("SELECT typCeny FROM objekty WHERE objekt='".$_POST["objekt"]."' LIMIT 1");
if ($zaznam = mysql_fetch_assoc($result))
	$_POST["typCeny"] = $zaznam["typCeny"];

$noci = (strtotime($_POST["konec"])-strtotime($_POST["nastup"]))/86400;
$nasobit = ($_POST["typCeny"]==0)?$noci:1;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Tisk rekreaèního poukazu</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="tisk.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="index.js"></script>
	<script language="JavaScript" src="poukaz.js"></script>
	<script>
		sazby = {
	<?php	if (empty($_POST["poukaz"])) {
			$neniPole = array("akce","ulozit","tisk","smazat","serad","objekt");
			akceSDbf("nacti","objekty",$neniPole,"objekt",$_POST["objekt"]);
		}
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
<body onLoad="<?php echo " prepocti(); popisy(".$_POST["typCeny"]."); getObj('tisk').printing.Print(true);";?>" leftmargin="20">
<h1>Rekreaèní poukaz è. <?php echo $_POST["zkratka"].$_POST["rok"].str_pad($_POST["poukaz"], 3, "0", STR_PAD_LEFT);?></h2>
<table cellspacing="0" cellpadding="0">
	<table class=h3 cellspacing="0" cellpadding="0">
	<tr><td>Objekt :</td><td><?php echo $_POST["nazev"];?></td></tr>
	<tr><td></td><td><?php echo $_POST["adresa"];?></td></tr>
	<tr><td></td><td>Tel.: <?php echo $_POST["telefon"];?></td></tr></table><br>
	<table cellpadding="0" cellspacing="0">
		<tr><td colspan="2">&nbsp;</td>
			<td colspan="4" align="left">Poèet osob:</td><td colspan="2">z toho bez poplatku</td></tr>
		<tr><td width="100">Zamìstnanec:</td>
			<td><?php echo $_POST["nazev_zam"];?></td>
			<td>dospìlí:</td><td><?php echo $_POST["osob_zam"];?></td>
			<td>dìti:</td><td><?php echo $_POST["osob_zad"];?></td>
			<td align="right"><?php echo $_POST["inv_zam"];?></td></tr>
		<tr><td>Cizí:</td>
			<td><?php echo $_POST["nazev_ciz"];?></td>
			<td>dospìlí:</td><td><?php echo $_POST["osob_ciz"];?></td>
			<td>dìti:</td><td><?php echo $_POST["osob_cid"];?></td>
			<td align="right"><?php echo $_POST["inv_ciz"];?></td></tr>
	</table>
	
	<table cellpadding="0" cellspacing="0" width="605">
	<tr><td colspan="4" height="8"></td></tr>
	<tr><td width="100">Jméno:</td><td style="font-weight : bold;"><?php echo $_POST["jmeno"]." ".$_POST["prijmeni"];?></td>
		<td></td><td></td></tr>
	<tr><td>Bydlištì:</td><td colspan="3"><?php echo $_POST["bydliste"];?></td></tr>
	</table>
	<table cellpadding="0" cellspacing="0" width="605">
	<tr><td width="100">Nástup:</td><td><?php echo DateEnCz($_POST["nastup"]);?></td>
		<td>ukonèení:</td><td><?php echo DateEnCz($_POST["konec"]);?></td>
		<td class="popis1">pokoj:</td><td class="popis2">chata:</td><td><?php echo $_POST["pokoj"];?></td><td></td></tr>
		<tr><td colspan="6" height="5"></td></tr>
	</table><br>
	<table class=tabulka cellspacing="0" cellpadding="0" width="720">
	<tr class=zahlavi>
	    <td></td>
	    <td style="width:50" class="popis1">poèet osob</td><td style="width:50" class="popis2">poèet</td>
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
	    <td><?php echo $_POST["c_zam_pou"];?> Kè</td>
	    <td><?php echo $_POST["c_zam_dph"];?> Kè</td>
	    <td><?php echo $_POST["c_zam_rek"];?> Kè</td>
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
	    <td><?php echo $_POST["c_zad_pou"];?> Kè</td>
	    <td><?php echo $_POST["c_zad_dph"];?> Kè</td>
	    <td><?php echo $_POST["c_zad_rek"];?> Kè</td>
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
	    <td><?php echo $_POST["c_ciz_pou"];?> Kè</td>
	    <td><?php echo $_POST["c_ciz_dph"];?> Kè</td>
	    <td><?php echo $_POST["c_ciz_rek"];?> Kè</td>
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
	    <td><?php echo $_POST["c_cid_pou"];?> Kè</td>
	    <td><?php echo $_POST["c_cid_dph"];?> Kè</td>
	    <td><?php echo $_POST["c_cid_rek"];?> Kè</td>
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
	<h3>K úhradì: <span id=kuhrade>0,00</span> Kè</h3>
	
	<input type="hidden" name="typCeny" value="<?php echo $_POST["typCeny"]?>">
	<input type="hidden" name="nazev_zam" value="<?php echo $_POST["nazev_zam"]?>">
	<input type="hidden" name="nazev_ciz" value="<?php echo $_POST["nazev_ciz"]?>">
	<input type="hidden" name="osob_zam" value="<?php echo $_POST["osob_zam"]?>">
	<input type="hidden" name="osob_zad" value="<?php echo $_POST["osob_zad"]?>">
	<input type="hidden" name="inv_zam" value="<?php echo $_POST["inv_zam"]?>">
	<input type="hidden" name="osob_ciz" value="<?php echo $_POST["osob_ciz"]?>">
	<input type="hidden" name="osob_cid" value="<?php echo $_POST["osob_cid"]?>">
	<input type="hidden" name="inv_ciz" value="<?php echo $_POST["inv_ciz"]?>">
	<input type="hidden" name="celkem" value="<?php echo $_POST["celkem"]?>">
	<input type="hidden" name="nastup" value="<?php echo DateEnCz($_POST["nastup"])?>">
	<input type="hidden" name="konec" value="<?php echo DateEnCz($_POST["konec"])?>">
	
	<textarea id=text style="display:none"><?php echo $_POST["text"];?></textarea>
	<textarea id=poznamka style="display:none"><?php echo $_POST["poznamka"];?></textarea>
	<table cellpadding="0" cellspacing="0"><tr><td><span id=pozn1></span><br><br>
	<span id=pozn2></span></td><td><?php if (!empty($_POST["obrazky"])) echo "<img src=\"obrazky/".$_POST["obrazky"]."\" alt=\"\">";?></td></tr></table>
	<script>
		getObj("pozn1").innerText = getObj("text").value;
		getObj("pozn2").innerText = getObj("poznamka").value;
	</script>
	<object id=tisk style="display:none"
	  classid="clsid:1663ed61-23eb-11d2-b92f-008048fdd814"
  	  codebase="http://intranet.ssok.cz/rekreace/ScriptX.cab#Version=6,2,433,14">
	</object>
	<script>
			getObj('tisk').printing.header = "";
			getObj('tisk').printing.footer = "";
			getObj('tisk').printing.portrait = true;
			getObj('tisk').printing.leftMargin = 0.0;
			getObj('tisk').printing.topMargin = 0.0;
			getObj('tisk').printing.rightMargin = 0.0;
			getObj('tisk').printing.bottomMargin = 0.0;
	</script>
</table>	
</html>
