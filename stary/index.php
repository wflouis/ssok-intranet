<?php
include "funkce/funkce.php"; 
if (!maPristup()) 
	exit;
include "funkce/databaze.php"; 
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<title>Intranet Správy silnic Olomouckého kraje</title>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="jquery.js"></script>
	<script language="JavaScript" src="funkce.js"></script>
	<script>
	<?php echo "Rozdil = new Date()-".time()."*1000;"; ?>
	</script>
</head>
<body onLoad="DnesJe();zmenaVelikosti();" onresize="zmenaVelikosti()">
<img class="logo" src="img/logo.png" alt="" border="0">
<span class=N4>Správa silnic Olomouckého kraje</span><br>
<span class=N2>Intranetový server pro interní komunikaci a sdílení dokumentů</span>
<iframe id="telo" src="uvod.php" frameborder="0"></iframe>
<table id="menu" class="menu" width="180" onMouseOver="rozsvit()" onMouseOut="zhasni()" border="0" cellspacing="0" cellpadding="0" onClick="vyber()">
	<tr id=1><td width="30" align="right">1.</td><td>Úvodní strana</td></tr>
	<tr id=21><td align="right">2.</td><td>Dokumenty SSOK</td></tr>
	<tr id=2><td align="right">3.</td><td>Interní dokumenty organizace</td></tr>
	<tr id=3><td align="right">4.</td><td>Smlouvy, registry</td></tr>
	<tr id=4><td align="right">5.</td><td>Zákony</td></tr>
	<tr id=8><td align="right">6.</td><td>Procesy SSOK</td></tr>
	<tr id=5><td align="right">7.</td><td>Rekreační péče</td></tr>
	<tr id=6><td align="right">8.</td><td>Telefonní seznam</td></tr>
	<tr id=7><td align="right">9.</td><td>Aktuality</td></tr>
	<tr id=9><td align="right">10.</td><td>Výběrová řízení</td></tr>
<!--	<tr id=10><td>Sledování záruk</td></tr>  !-->
	<tr id=11><td align="right">11.</td><td>Smluvní partneři</td></tr>
	<tr id=12><td align="right">12.</td><td>Smlouvy</td></tr>
<!--	<tr id=13><td align="right">13.</td><td>Směrnice</td></tr> !-->
	<tr id=15><td align="right">13.</td><td>Bezpečnost práce</td></tr>
	<tr id=16><td align="right">14.</td><td>Portál PO</td></tr>
	<tr id=17><td align="right">15.</td><td>GDPR</td></tr>
</table>
<div id=AktCas class="N1 poznamka"></div>
<div id=svatek class="N1 poznamka">Svátek má<br>
<?php
  $result = mysqli_query($_SESSION["link"],"select svatek FROM svatky WHERE mesic = '".date("n")."' and den = '".date("d")."'");
  $radek = mysqli_fetch_assoc($result); 
  echo $radek["svatek"];
?>
</div>
<?php
	include "upozornit.php"; 
?>
</body>
</html>


