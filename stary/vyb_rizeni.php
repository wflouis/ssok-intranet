<?php
define("MAX_VEL_PRILOHY", 2000000);
include "funkce/funkce.php"; 
if (!maPristup("V")) 
	exit;

if (isset($_GET["soubor"]) && !empty($_GET["soubor"]) && file_exists("documents/".basename($_GET["soubor"]))) { 
	Header("Location: documents/".basename($_GET["soubor"]));
	exit;
}
$_POST["kontakt"]=$_SESSION['id_jmeno'];

include "funkce/databaze.php"; 
$result = mysql_query("SELECT * FROM seznam WHERE id_jmeno='".$_POST["kontakt"]."'");
$radek = mysql_fetch_assoc($result);
$chyba = "";
$_SESSION["zadavajici"] = $radek["jmeno"].", ".$radek["funkce"].", ".$radek["telefon"];

$_POST["datum"] = DateCzEn($_POST["datum"]);
$_POST["datumStazeni"] = DateCzEn($_POST["datumStazeni"]);
$_POST["termin"] = DateCzEn($_POST["termin1"])." ".$_POST["termin2"];
if ($_FILES['soubor']['size']>MAX_VEL_PRILOHY) 
	$chyba = "Chyba: Soubor pøílohy je pøíliš velký. Zmenšete pøedevším velikost obrázkù v nìjakém grafickém editoru!!!";
else
	if (!empty($_FILES['soubor']) && ($_FILES['soubor']['size']>0) && (move_uploaded_file($_FILES['soubor']['tmp_name'], "./documents/".$_FILES['soubor']['name']))) 
	  { 
	    $_POST["priloha"] = $_FILES['soubor']['name'];
	  }; 

switch ($_POST["akce"]) {
	case "Uložit": 
		if (!empty($_POST["cj"]))
			uloz("vyb_rizeni",$_POST,$_POST["id_vr"]);
		break;
	case "Archivovat": 
		if (!empty($_POST["id_vr"]))
			$result = mysql_query("UPDATE vyb_rizeni SET archiv = '1', priloha = '' WHERE id_vr = '".$_POST["id_vr"]."'"); break;
	case "Zpìt":
	case "Archiv": $_POST["archiv"]=($_POST["archiv"]+1)%2; 
	case "Nový záznam":
		$_POST = array ("datum" => date("Y-m-d",time()), "termin2" => "12:00", "archiv" => $_POST["archiv"], "kontakt" => $_POST["kontakt"], "poznamka" => "Žádám Vás o zaslání písemné žádosti o poskytnutí zadávací dokumentace, nebo webové stránky naší organizace nejsou atestovaným elektronickým nástrojem ve smyslu §§ 48 odst. 2 a 149 odst. 2 zákona 137/2006 Sb. o veøejných zakázkách v platném znìní.");
}
if (!empty($_POST["id_vr"])) {
	$query   = "SELECT * FROM vyb_rizeni WHERE id_vr='".$_POST["id_vr"]."' LIMIT 1";
	$result = mysql_query($query);
	if (mysql_num_rows($result)>0 and $radek = mysql_fetch_assoc($result))
		DbToPOST($radek);
	$termin = explode(" ",$_POST["termin"]);
	$_POST["termin1"] = DateEnCz($termin[0]);
	$_POST["termin2"] = substr($termin[1],0,5);
}
if (empty($_POST["archiv"]))
	$_POST["archiv"]=0;
$_POST["datum"] = DateEnCz($_POST["datum"]);
$_POST["datumStazeni"] = DateEnCz($_POST["datumStazeni"]);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="funkce.js"></script>
	<?php
	if ($_POST["archiv"]==0)
	   echo "<LINK href=\"rizeni.css\" type=text/css rel=stylesheet>\n";
	?>
</head>
<body <?php echo ($_POST["archiv"]==0)?"onload=\"getObj('cj').focus()\"":""?>>
<p class=N3>Výbìrová øízení<?php echo ($_POST["archiv"]==1)?" - archiv":""; ?></p>
<?php echo "<div class=chyba>".$chyba."</div>";?>
<form name="razeni" action="vyb_rizeni.php" method="post" ENCTYPE="multipart/form-data"><input type="hidden" id="id_vr" name="id_vr" value="<?php echo $_POST["id_vr"];?>">
<input type="hidden" name="archiv" value="<?php echo $_POST["archiv"];?>">
<div class=form1>
<?php
if ($_POST["archiv"]==0)
	include "vr_form.php"; 
?>
<div class=txVpravo><input type="submit" name="akce" value="<?php echo ($_POST["archiv"]==0)?"Archiv":"Zpìt"; ?>"></div>
</div>
</form>
<div>
<?php
@$result = mysql_query("SELECT v.*, jmeno, funkce, telefon FROM vyb_rizeni v join seznam s where id_jmeno=kontakt and (datumStazeni>NOW() xor ".$_POST["archiv"].") and kontakt like '".$_POST["kontakt"]."' ORDER BY datum DESC");
$cisloRadku = 0;
echo "<table>\n";
echo "<tr class=HlTab><td>è.j.</td><td>datum vyhlášení</td><td>kontakt zadavatele</td><td>pøedmìt zakázky</td><td>termín pro podání nabídek</td><td>pøílohy</td></tr>\n";
while ($radek = mysql_fetch_assoc($result)) {
  echo "<TR id=".$radek["id_vr"];
  if ($cisloRadku%2==0)
    echo " class=licha>\n";
  else
    echo " class=suda>\n";
  echo "<TD>".$radek["cj"]."</TD>\n";
  echo "<TD>".date("d.m.Y",strtotime($radek["datum"]))."</TD>\n";
  echo "<TD>".$radek["jmeno"].", ".$radek["funkce"].", ".$radek["telefon"]."</TD>\n";
  echo "<TD>".$radek["predmet"]."</TD>\n";
  echo "<TD>".date("d.m.Y \d\o G:i",strtotime($radek["termin"]))."</TD>\n";
  echo "<TD><a href=\"vyb_rizeni.php?soubor=".$radek["priloha"]."\">".$radek["priloha"]."</a></TD>\n";
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
</body>
</html>
