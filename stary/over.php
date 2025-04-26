<?php
include_once "funkce/funkce.php"; 

if (isset($_SERVER['PHP_AUTH_USER']) || isset($_COOKIE["intranet"]) || isset($_GET["kod"])) { 
	include_once "funkce/databaze.php"; 
	if (!empty($_GET["kod"]))
		$podminka = $_GET["kod"];
	else if (!empty($_COOKIE["intranet"]))
		$podminka = $_COOKIE["intranet"];
	else if (!empty($_SERVER['PHP_AUTH_USER']))
		$podminka = $_SERVER['PHP_AUTH_USER'];
	else 
		$podminka = md5(rand());
	setcookie("intranet", $_COOKIE["intranet"], time()- 3600*24);
	$Novy = md5(rand());
	$query = "SELECT id_jmeno, heslo, kod, stredisko FROM seznam WHERE email='$podminka' or kod = '$podminka' LIMIT 1";
	$result = mysqli_query($_SESSION["link"],$query);  //echo $query;
	if (mysqli_num_rows($result)==1) {
		$radek = mysqli_fetch_assoc($result);
		session_start();
		$RIp = vratIP();
		$query = "UPDATE seznam SET kod = '$Novy', ip = '".$RIp[0]."' WHERE id_jmeno = '".$radek["id_jmeno"]."' LIMIT 1";
		if ($radek["kod"] == $podminka || (!empty($_SERVER['PHP_AUTH_PW']) && $radek["heslo"] == md5($_SERVER['PHP_AUTH_PW']))) {
			$_SESSION["prava"] = "X"; 
			$result = mysqli_query($_SESSION["link"],"SELECT zkratka FROM moduly m, pristPrava p WHERE m.id_modulu = p.id_modulu and id_jmeno = '".$radek["id_jmeno"]."'");
			while($zkratky=mysqli_fetch_assoc($result))
				$_SESSION["prava"] .= $zkratky["zkratka"];
			setcookie("intranet", $Novy, time()+ 3600*24*365);
			setcookie("id_jmeno", $radek["id_jmeno"], time()+ 3600*24*365);
			$_SESSION["id_jmeno"] = $radek["id_jmeno"];
			$result = mysqli_query($_SESSION["link"],"SELECT pristup FROM seznam_str WHERE zkratka='".$radek["stredisko"]."' LIMIT 1"); 
			$dleStrediska = mysqli_fetch_assoc($result);
			$_SESSION["dleStrediska"] = $dleStrediska["pristup"]; 
			mysqli_query($_SESSION["link"],$query);
			//  záznam historie
		    $result = mysqli_query($_SESSION["link"],"INSERT INTO historie VALUES ('".$_SESSION["id_jmeno"]."',NOW())");
			Header("Location: index.php");
			exit;
		} else 
			if (empty($_SERVER['PHP_AUTH_PW'])) {
//				$handle = fopen ("hlava_reg.html", "r");
				$handle = fopen ("hlava_reg.txt", "r");
				$zprava = "";
				mysqli_query($_SESSION["link"],$query);
				while (!feof ($handle)) {
				    $buffer = fgets($handle);
				   	$zprava .= $buffer;
				}
//				$zprava .= "<input type=\"hidden\" name=\"kod\" value=\"$Novy\"></form></body></html>";
				$zprava .= "http://intranet.ssok.cz/over.php?kod=$Novy";
				fclose ($handle);
				$headers = "From: Intranet <ulmann@aito.cz>\n";
				$headers .= "X-Sender: <ulmann@aito.cz>\n";
				$headers .= "X-Mailer: PHP\n"; 
				$headers .= "X-Priority: 1\n"; 
				$headers .= "Return-Path: <ulmann@aito.cz>\n";  
//				$headers .= "Content-Type: text/html; charset=Windows-1250\n"; 
				$headers .= "Content-Type: text/plain; charset=Windows-1250\n"; 
				mail($_SERVER['PHP_AUTH_USER'], "Aktivujte pøístup do modifikace aktualit!", $zprava, $headers);
			}
	}
}
if (isset($_GET["login"]) && empty($zprava))
	Header("WWW-Authenticate: Basic realm=\"Pøihlášení do Intranetu\"");

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
</head>
<body leftmargin="120" topmargin="16" bottommargin="0" rightmargin="60" <?php echo (!isset($_GET["login"])?"onLoad=\"location.replace('over.php?login=1');\"":"") ?>>
<img src="img/logo.gif" alt="" width="82" height="83" border="0" style="position:absolute; left=0;top=0">
<div class=N4>Správa silnic Olomouckého kraje</div>
<div class=N2>Intranetovı server pro interní komunikaci a sdílení dokumentù</div>
<br>
<p class=norm>Tento Intranet je interní datová sluba urèená pouze pro vnitøní potøebu Správy silnic Olomouckého kraje. Pøístup k intranetu SSOK získáte 
zadáním své e-mailové adresy jako uivatelské jméno. popø. hesla, pokud se pøihlašujete z jiného poèítaèe ne obvykle.</p>
<?php if (isset($zprava))
echo "<p class=\"norm chyba\">Na vaši e-mailovou adresu byl zaslán ovìøovací formuláø, kde mùete aktivovat váš pøístup k Intranetu.</p>";
else
echo "<p class=\"norm chyba\">Došlo ke zmìnì v pøihlašování k Intranetu. Pokud nemáte vytvoøen pøístup, mùete si jej aktivovat pomocí ovìøovacího formuláøe, 
kterı vám bude zaslán na e-mailovou adresu, uvedenou jako uivatelské jméno pøi pokusu o <a href=\"over.php?login=1\">pøihlášení</a>. E-mailová adresa se musí shodovat s vaší adresou, uvedenou v telefonním seznamu SSOK!</p>
<p class=norm>Po úspìšném ovìøení budou ji pøíštì stránky intranetu nabíhat automaticky. ";
?>
Máte-li jakékoliv potíe se vstupem do intranetu, obrate se na:</p>
<a class=mail href="mailto:kozakova@ssok.cz">paní Zlatku Kozákovou</a> Tel.:585 170 337 - dodavatele veškerıch písemností, dokumentù a úvodních námìtù, nebo na<br>
<a class=mail href="mailto:ulmann@scomeq.cz">Ing. Antonína Ulmanna</a> Tel.:603 469 824 - technického realizovatele této aplikace
</body>
</html>

