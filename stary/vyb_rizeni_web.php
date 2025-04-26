<?php
session_start();
include "funkce/databaze.php";  
if (!empty($_POST["id_vr"]) && !empty($_POST["email"])) { 
    @$result = mysql_query("SELECT v.*, jmeno, funkce, telefon, email FROM vyb_rizeni v join seznam s where id_jmeno=kontakt and id_vr='".$_POST["id_vr"]."'");
	$radek = mysql_fetch_assoc($result); 
	if(eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$", $_POST['email']) && file_exists("documents/".$radek["priloha"])) {
		$file = "documents/".$radek["priloha"];
	    $mime_boundary=md5(time());
	
		$headers = "Content-Type: multipart/mixed; boundary=\"".$mime_boundary."\"\n";
		$headers .= "From: Výbìrová øízení <".$radek["email"].">\n";
		$headers .= "X-Sender: <".$radek["email"].">\n";
		$headers .= "X-Mailer: PHP\n"; 
		$headers .= "X-Priority: 1\n"; 
		$headers .= "Return-Path: <ulmann@scomeq.cz>\n";  
	
	    $msg = "--".$mime_boundary."\n";
		$msg .= "Content-Type: text/html; charset=Windows-1250\n"; 
	    $msg .= "Content-Transfer-Encoding: 8bit\n\n";
	    $msg .= "V pøíloze tohoto mailu najdete vyžádanou pøílohu k vybranému výbìrovému øízení Správy silnic Olomouckého kraje:\r\n\r\n";
		$msg .= $radek["predmet"]."\n\n";
	
	    $msg .= "--".$mime_boundary."\n";
	    $msg .= "Content-Type: ".filetype($file)."; name=\"".$radek["priloha"]."\"\n";  
	    $msg .= "Content-Transfer-Encoding: base64\n";
	    $msg .= "Content-Description: ".$radek["priloha"]."\n";
	    $msg .= "Content-Disposition: attachment; filename=\"".$radek["priloha"]."\"\n\n"; 
	    $handle = fopen($file, 'rb');
	    $f_contents = fread($handle, filesize($file));
	    $msg .= chunk_split(base64_encode($f_contents))."\n\n";  
	    fclose($handle);
	    $msg .= "--".$mime_boundary."--\n\n";
	   
		mail($_POST['email'], "Vyžádaná dokumentace k výbìrovému øízení", $msg, $headers);
		
	    $msg = "--".$mime_boundary."\n";
		$msg .= "Content-Type: text/html; charset=Windows-1250\n"; 
	    $msg .= "Content-Transfer-Encoding: 8bit\n\n";
	    $msg .= "Zájemce z e-mailové adresy ".$_POST['email']." si stáhnul dokumentaci k výbìrovému øízení:\n\n";
		$msg .= $radek["predmet"]."\n\n";
  	    $msg .= "--".$mime_boundary."--\n\n";
		mail($radek["email"], "Informace o stažení dokumentace k výbìrovému øízení", $msg, $headers);
		$stav = 1;
	}
	Header("Location: odeslano.php?stav=".$stav);
}
$result = mysql_query("UPDATE vyb_rizeni SET archiv = '1' WHERE termin < NOW() and archiv = '0'"); 
if (empty($_POST["archiv"]))
	$_POST["archiv"]=0;
switch ($_POST["akce"]) {
	case "Zpìt":
	case "Archiv": $_POST["archiv"]=($_POST["archiv"]+1)%2; 
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="http://www.ssok.cz/ssok.css" type=text/css rel=stylesheet>
	<LINK href="http://www.ssok.cz/rizeni.css" type=text/css rel=stylesheet>
</head>
<body>
<p class=N3>Výbìrová øízení:<?php echo ($_POST["archiv"]==1)?" - archiv":""; ?></p>
<form name="razeni" action="vyb_rizeni_web.php" method="post">
<input type="hidden" name="archiv" value="<?php echo $_POST["archiv"];?>">
<div class=form1>
<div><input type="submit" name="akce" value="<?php echo ($_POST["archiv"]==0)?"Archiv":"Zpìt"; ?>"></div>
</div>
</form>
<div>
<?php
@$result = mysql_query("SELECT v.*, jmeno, funkce, telefon FROM vyb_rizeni v join seznam s where id_jmeno=kontakt and datum<=NOW() and (datumStazeni>NOW() xor ".$_POST["archiv"].") ORDER BY datum DESC");
$cisloRadku = 0;
echo "<table cellspacing=\"0\">\n";
echo "<tr class=nadpis><td>è.j.</td><td>datum vyhlášení</td><td>zadavatel</td><td>kontakt zadavatele</td><td>pøedmìt zakázky</td><td>termín pro podání nabídek</td><td>pøílohy</td></tr>\n";
while ($radek = mysql_fetch_assoc($result)) {
  echo "<TR id=".$radek["id_vr"];
  if ($cisloRadku%2==0)
    echo " class=licha>\n";
  else
    echo " class=suda>\n";
  echo "<TD>".$radek["cj"]."</TD>\n";
  echo "<TD>".date("d.m.Y",strtotime($radek["datum"]))."</TD>\n";
  echo "<TD>Správa silnic Olomouckého kraje, Lipenská 120, 772 11 Olomouc</TD>\n";
  echo "<TD>".$radek["jmeno"].", ".$radek["funkce"].", ".$radek["telefon"]."</TD>\n";
  echo "<TD>".$radek["predmet"]."</TD>\n";
  echo "<TD>".date("d.m.Y \d\o G:i",strtotime($radek["termin"]))."</TD>\n";
  echo "<TD><span class=\"dokumentace\">".((!empty($radek["priloha"]) and $_POST["archiv"]==0)?"text zadávací dokumentace":"")."</span></TD>\n";
//  echo "<TD><span class=\"dokumentace\"><a href=\"http://www.ssok.cz/kestazeni/ZadPodminky.doc\">text zadávací dokumentace</a></span></TD>\n";
  echo "</TR>\n";
  echo "<TR";
  if ($cisloRadku%2==0)
    echo " class=\"licha\">\n";
  else
    echo " class=\"suda\">\n";
  echo "<TD colspan=\"7\">".$radek["poznamka"]."</TD>\n";
  echo "</TR>\n";
  $cisloRadku += 1;
}
mysql_Close($_SESSION["link"]);
if ($cisloRadku == 0) 
  echo "<TR><TD colspan=7 align=\"center\">Momentálnì neprobíhá žádné výbìrové øízení.</TD></TR>";
echo "</table>\n";
?>
</div>
<!--<div id="odeslat">
<form action="vyb_rizeni_web.php" method="post" enctype="multipart/form-data"><input type="hidden" id="id_vr" name="id_vr">
Dokumentaci zaslat na e-mailovou adresu: <br><input type="text" name="email" size="30" maxlength="30"><br> <input type="submit" value="Odeslat">
</form>
</div>!-->
</body>
</html>
