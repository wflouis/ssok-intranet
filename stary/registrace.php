<?php
include "funkce/databaze.php"; 
include "funkce/funkce.php"; 
if (!maPristup()) 
	exit;
if (!empty($_POST["kod"]) || !empty($HTTP_COOKIE_VARS["aktuality"])) {
	if (empty($_POST["kod"]))
		$_POST["kod"] = $HTTP_COOKIE_VARS["aktuality"];
	$vypis = mysql_query("SELECT jmeno FROM uzivatele WHERE kod = '".$_POST["kod"]."' LIMIT 1");
	if (mysql_num_rows($vypis) == 1) {
		$radek = mysql_fetch_assoc($vypis);
		$_SESSION["jmeno"] = $radek["jmeno"];
		$Novy = md5(rand());
		setcookie("aktuality", $HTTP_COOKIE_VARS["aktuality"], time()- 3600*24);
		setcookie("aktuality", $Novy, time()+ 3600*24*365);
		$vlozeno = mysql_query("UPDATE uzivatele SET kod = '$Novy', aktivni = '1' WHERE kod = '".$_POST["kod"]."' LIMIT 1");
	}
}
switch ($_POST["stredisko"]) {
	 case '2': $Nazev='SSOK'; break;
	 case '3': $Nazev='SUOl'; break;
	 case '4': $Nazev='CeOl'; break;
	 case '5': $Nazev='CeLi'; break;
	 case '6': $Nazev='CeSt'; break;
	 case '7': $Nazev='SUJi'; break;
	 case '8': $Nazev='CePv'; break;
	 case '9': $Nazev='CeKo'; break;
	 case '11': $Nazev='CePr'; break;
	 case '12': $Nazev='CeHr'; break;
	 case '13': $Nazev='SUSu'; break;
	 case '14': $Nazev='CeVi'; break;
	 case '15': $Nazev='CeHa'; break;
	 case '16': $Nazev='CeJe'; break;
	 case '17': $Nazev='CeZa'; break;
	 case '18': $Nazev='CeMo'; break;
	 default: $Nazev='nic';
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
</head>
<body leftmargin="20" topmargin="20" bottommargin="20" rightmargin="20">
<?php	if (isset($_SESSION["jmeno"]))
		echo "<script>document.location=\"zpravy.php\"</script>";
?>
<p class=N3>Zadání a modifikace aktualit</p>
<p class=norm>K zadávání a modifkaci aktualit se musíte nejdøíve identifikovat. Vyberte støedisko, na kterém jste evidován(a) a následnì vyberte své jméno. V seznamu jsou uvedena pouze jména s e-mailovou schránkou dle telefonního seznamu, musíte tedy mít již pøidìlenu svoji e-mailovou schránku. Na tento e-mail Vám bude zaslán registraèní formuláø, jehož potvrzením aktivujete pøístup do nabídky aktualit.</p>
<form name=registrace action="registrace.php" method="post">
<select name="stredisko" onChange="document.registrace.akce.value='nacti'; document.registrace.submit()">
	<option value="1"></option>
	<option value="2">Øeditelství SSOK</option>
	<option value="3">Støedisko údržby Olomouc</option>
	<option value="4">Cestmistrovství Olomouc</option>
	<option value="5">Cestmistrovství Litovel</option>
	<option value="6">Cestmistrovství Šternberk</option>
	<option value="7">Støedisko údržby Jih</option>
	<option value="8">Cestmistrovství Prostìjov</option>
	<option value="9">Cestmistrovství Konice</option>
	<option value="11">Cestmistrovství Pøerov</option>
	<option value="12">Cestmistrovství Hranice</option>
	<option value="13">Støedisko údržby Šumperk</option>
	<option value="14">Cestmistrovství Vikýøovice</option>
	<option value="15">Cestmistrovství Hanušovice</option>
	<option value="16">Cestmistrovství Jeseník</option>
	<option value="17">Cestmistrovství Zábøeh</option>
	<option value="18">Cestmistrovství Mohelnice</option>
</select>
<script>document.registrace.stredisko.value=
<?php  if (empty($_POST["stredisko"]))
		echo "1";
	else
  		echo $_POST["stredisko"]; ?>;</script>
<select name="email" onChange="document.registrace.jmeno.value=this[this.value].innerText">
	<option value="0" SELECTED></option>
<?php	$jmena=mysql_query("SELECT jmeno FROM seznam WHERE stredisko LIKE '$Nazev'");
	$pocet=mysql_num_rows($jmena);
	for($i=1; $i<=$pocet; $i++) {
		$radek = mysql_fetch_assoc($jmena);
		echo "<option value=\"$i\">".$radek["jmeno"]."</option>\n";
	}
?>
</select>
<input name="jmeno" type="hidden" value="">
<input name="akce" type="hidden" value=""><br><br>
<input type="submit" value="Zaslat registraèní formuláø"></form>
<?php	if (empty($_POST["akce"]) && !empty($_POST["jmeno"])) {
		$jmena=mysql_query("SELECT email FROM seznam WHERE jmeno = '".$_POST["jmeno"]."' LIMIT 1");
		if (mysql_num_rows($jmena)==1) {
			$radek = mysql_fetch_assoc($jmena);
			$Novy = md5(rand());
			$vlozeno = mysql_query("INSERT INTO uzivatele VALUES('".$_POST["jmeno"]."','$Novy',NOW(),'0')");
			echo "<div class=N0>Registraèní formuláø byl zaslán na e-mailovou adresu: ".$radek["email"].".</div><br><div class=N0>Nahlédnìte nyní do své e-mailové schránky. V obdrženém formuláøi tlaèítkem <em>\"Aktivovat\"</em> aktivujte pøístup do modifikace aktualit.</div>";
//			$handle = fopen ("hlava_reg.html", "r");
			$handle = fopen ("hlava_reg.txt", "r");
			while (!feof ($handle)) {
			    $buffer = fgets($handle);
			   	$zprava .= $buffer;
			}
			fclose ($handle);
//			$zprava .= "<input type=\"hidden\" name=kod value=\"$Novy\">\n </form></body></html>";
			$zprava .= "http://intranet.ssok.cz/over.php?kod=$Novy";
			$headers .= "From: Intranet <ochmannova@ssok.cz>\n";
			$headers .= "X-Sender: <ochmannova@ssok.cz>\n";
			$headers .= "X-Mailer: PHP\n"; 
			$headers .= "X-Priority: 1\n"; 
			$headers .= "Return-Path: <ulmann@scomeq.cz>\n";  
//			$headers .= "Content-Type: text/html; charset=Windows-1250\n"; 
			$headers .= "Content-Type: text/plain; charset=Windows-1250\n"; 
			mail($radek["email"], "Aktivujte pøístup do modifikace aktualit!", $zprava, $headers);
		} else
			echo "<div class=N0>Nepodaøilo se zaslat registraèní formuláø, kontaktujte <a href=\"mailto: ulmann@scomeq.cz\">správce aplikace</a>!</div>";
	}
?>
</body>
</html>
