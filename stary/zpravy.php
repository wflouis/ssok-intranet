<?php
include "funkce/funkce.php"; 
if (!maPristup("A")) 
	exit;
include "funkce/databaze.php"; 
$zaznam = array("text"=>"","datum"=>""); 
if (empty($_POST["radek"])) {
	$_POST["radek"] = ""; 
	if (!empty($_POST["zprava"])) 
		$zpravy=mysqli_query($_SESSION["link"],"INSERT INTO zpravy VALUES ('', '".$_SESSION["id_jmeno"]."',NOW(),'".$_POST["zprava"]."')");
} else { 
	if ($_POST["akce"] == "nacti") {
		$Sql  = "SELECT * FROM zpravy WHERE id_jmeno = '".$_SESSION["id_jmeno"]."' ORDER BY datum desc LIMIT ".($_POST["radek"]-1).",1";
		$zpravy=mysqli_query($_SESSION["link"],$Sql);
		if (mysqli_num_rows($zpravy)==1) 
			$zaznam = mysqli_fetch_assoc($zpravy);
	} else {
		if (empty($_POST["zprava"])) 
			$zpravy=mysqli_query($_SESSION["link"],"DELETE FROM zpravy WHERE id_jmeno = '".$_SESSION["id_jmeno"]."' and datum = '".$_POST["datum"]."' LIMIT 1");
		else 
			$zpravy=mysqli_query($_SESSION["link"],"UPDATE zpravy SET text = '".$_POST["zprava"]."' WHERE id_jmeno = '".$_SESSION["id_jmeno"]."' and datum = '".$_POST["datum"]."' LIMIT 1"); 
		$_POST["radek"] = "";
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
</head>
<script language="JavaScript">
function nacti() {
	if (event.srcElement.parentElement.id != "") {
		document.zpravy.akce.value="nacti";
		document.zpravy.radek.value=event.srcElement.parentElement.id;
		document.zpravy.submit();
	}
}
</script>
<body leftmargin="20" topmargin="20" bottommargin="20" rightmargin="20">
<p class=N3>Zadání a modifikace aktualit</p>
<p class=norm>Vyplòte pole pro zprávu a potvïte tlaèítkem <em>Odeslat</em>. Údaje o autorovi zprávy a èasu vložení se doplní automaticky. V dolní èásti se zobrazí pouze Vaše zprávy, které máte možnost opravit nebo zcela vymazat. Vymazání zprávy provedete vymazáním textu, naèteného v poli pro editaci právy a odesláním prázdného pole.</p>
<form name=zpravy action="zpravy.php" method="post"><textarea class=text cols="70" rows="3" name="zprava"><?php echo $zaznam["text"];?></textarea><br><br><input type="hidden" name="radek" value="<?php echo $_POST["radek"];?>"><input type="hidden" name="datum" value="<?php echo $zaznam["datum"];?>"><input name="akce" type="hidden" value=""><input type="submit" value="Odeslat"></form>
<table cellpadding="3" cellspacing="0" width="600" onClick="nacti()">
<?php 
	$Sql  = "SELECT * FROM zpravy WHERE id_jmeno = '".$_SESSION["id_jmeno"]."' ORDER BY datum desc";
	$zpravy=mysqli_query($_SESSION["link"],$Sql);
	$pocet=mysqli_num_rows($zpravy);
	for($i=1; $i<=$pocet; $i++) {
		$zaznam = mysqli_fetch_assoc($zpravy);
		echo "<tr class=suda id=\"$i\"><td width=\"190\">Ze dne: ";
		echo DateEnCz(substr($zaznam["datum"],0,10)).substr($zaznam["datum"],10,9)."</td><td width=\"470\"></td></tr>\n";
		echo "<tr><td colspan=\"2\" class=poznamka>Text: ".$zaznam["text"]."</td></tr>\n";
	}
?>
</table>
</body>
</html>
