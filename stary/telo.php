<?php
include "funkce/funkce.php"; 
if (!maPristup()) 
	exit;
?>	
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="funkce.js"></script>
</head>
<body leftmargin="0" topmargin="40" bottommargin="0" rightmargin="0" onLoad="DnesJe()">
<iframe id=obsah src="uvod.php" frameborder="0"></iframe>
<table id=dokumenty cellspacing="0" cellpadding="0"><tr><td>
<table width="190" class=menu onMouseOver="rozsvit(1)" onMouseOut="zhasni(1)" border="0" cellspacing="0" cellpadding="3" onClick="vyber()">
<tr id=10><td>Zřizovací listina</td></tr>
<tr id=11><td>Výpis z OR</td></tr>
<tr id=12><td>Osvědčení o DIČ</td></tr>
<tr id=13><td>Organizační řád</td></tr>
<tr id=14><td>Organizační struktura</td></tr>
<tr id=15><td>Kolektivní smlouva</td></tr>
</table></td></tr></table>
<table class=menu width="190" onMouseOver="rozsvit()" onMouseOut="zhasni()" border="0" cellspacing="0" cellpadding="3" onClick="vyber()">
	<tr id=1><td>Úvodní strana</td></tr>
	<tr onMouseOver="getObj('dokumenty').style.display = 'block';"><td>Dokumenty SSOK</td></tr>
	<tr id=2><td>Vnitropodnikové předpisy</td></tr>
	<tr id=3><td>Smlouvy, registry</td></tr>
	<tr id=4><td>Zákony</td></tr>
	<tr id=5><td>Rekreační péče</td></tr>
	<tr id=6><td>Telefonní seznam</td></tr>
	<tr id=7><td>Aktuality</td></tr>
</table>
<table cellpadding="0" cellspacing="0" height="50">
<tr><td id=AktCas class="N1 poznamka" valign="bottom"></td></tr></table>
</body>
</html>
