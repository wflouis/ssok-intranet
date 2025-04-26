<?php
session_start();
$server="http://intranet.ssok.cz";
$strana = "rizeni";
include "funkce/databaze.php";  

if (isset($_GET["soubor"])) {
	$najdi = explode("-",$_GET["soubor"]); 
	@$prilohy = mysql_query("SELECT * FROM vybRizeniPrilohy WHERE id_vr='".$najdi[0]."' and id_verze='".$najdi[1]."' and cislo='".$najdi[2]."'");
	if ($priloha = @mysql_fetch_assoc($prilohy)) {
		header('Content-Description: File Transfer');
	    header('Content-Type: application/octet-stream');
	    header('Content-Disposition: attachment; filename="'.$priloha["popis"].'"');
	    header('Content-Transfer-Encoding: binary');
	    header('Expires: 0');
	    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	    header('Pragma: public');
	    header('Content-Length: ' . filesize("rizeni/".$_GET["soubor"]));
	    ob_clean();
	    flush();
	    readfile("rizeni/".$_GET["soubor"]);
	    exit;
	}
}
if (!empty($_GET["id"])) { 
	@$result = mysql_query("SELECT max(id_verze) as verze FROM vyberovaRizeni WHERE id_vr='".$_GET["id"]."'");
	if ($radek = @mysql_fetch_assoc($result))
		$maxVerze = $radek["verze"];
	if (!isset($_GET["verze"])) {
		$_GET["verze"]=$maxVerze;
	}
	@$result = mysql_query("SELECT * FROM vyberovaRizeni WHERE id_vr='".$_GET["id"]."' and id_verze='".$_GET["verze"]."'");
	$radek = @mysql_fetch_assoc($result); 
}
if (empty($_POST["archiv"]))
	$_POST["archiv"]=0;
switch ($_POST["akce"]) {
	case "Zpìt":
	case "Archiv": $_POST["archiv"]=($_POST["archiv"]+1)%2; 
}
readfile("http://www.ssok.cz/hlava.php?strana=rizeni"); 
?>
<div class="hlavniOkno">
	<h3>Výbìrová øízení:<?php echo (($_POST["archiv"]==1)?" archiv":"").($radek["zruseno"])?" (zrušeno)":""; ?></h3>
	<?php
		if (isset($_GET["id"])) {
			echo "<table cellspacing=\"5\">";
			echo "<tr class=\"oranz\"><td colspan=\"2\"><h2>".$radek["nazev"]."</h2></td></tr>";
			echo "<tr><td class=\"nadpis\">Datum uveøejnìní:</td><td>".date("d.m.Y",strtotime($radek["zverejnit"]))."</td></tr>";
			echo "<tr><td class=\"nadpis\">Verze:</td><td>".(($maxVerze==$radek["id_verze"])?"Aktuální":$radek["id_verze"])." (".date("d.m.Y G:i",strtotime($radek["zmena"])).")</td></tr>";
			if (!empty($radek["poznamka"]))
				echo "<tr><td class=\"nadpis\">Poznámka k verzi:</td><td>".$radek["poznamka"]."</td></tr>";
			echo "<tr><td colspan=\"2\"><h2>Informace o veøejné zakázce</h2></td></tr>";
			echo "<tr><td class=\"nadpis\">Název:</td><td>".$radek["nazev"]."</td></tr>";
			echo "<tr><td class=\"nadpis\">Druh:</td><td>".$radek["druh"]."</td></tr>";
			echo "<tr><td class=\"nadpis\">Popis:</td><td>".$radek["popis"]."</td></tr>";
			echo "<tr><td class=\"nadpis\">Pøedpokládaná hodnota bez DPH:</td><td>".(($radek["skrytCenu"])?"Neuvedena":number_format($radek["cena"], 0, ',', ' ').",-Kè")."</td></tr>";
			echo "<tr><td class=\"nadpis\">Druh zadávacího øízení:</td><td>".$radek["rozsahVR"]."</td></tr>";
			echo "<tr><td class=\"nadpis\">Hlavní místo plnìní:</td><td>Olomoucký kraj</td></tr>";
			@$cpvKody = mysql_query("SELECT text FROM cpvKody WHERE kod='".$radek["cpv"]."' LIMIT 1");
			if ($kod=@mysql_fetch_assoc($cpvKody)) 
				$text = $kod["text"];
			echo "<tr><td class=\"nadpis\">Hlavní pøedmìt:</td><td>".$radek["cpv"]." $text</td></tr>";
			echo "<tr><td class=\"nadpis\">Datum ukonèení pøíjmu nabídek:</td><td>".date("d.m.Y v G:i",strtotime($radek["lhuta"]))."</td></tr>";
			echo "<tr><td colspan=\"2\"><h2>Informace o zadavateli</h2></td></tr>";
			echo "<tr><td class=\"nadpis\">Název:</td><td>Správa silnic Olomouckého kraje, pøíspìvková organizace </td></tr>";
			echo "<tr><td class=\"nadpis\">IÈ:</td><td>70960399 </td></tr>";
			echo "<tr><td class=\"nadpis\">Adresa:</td><td>Olomouc, Lipenská 120, PSÈ 772 11 </td></tr>";
			@$kontakty = mysql_query("SELECT jmeno, telefon FROM seznam where id_jmeno='".$radek["zadal"]."' LIMIT 1");
			if ($kontakt = @mysql_fetch_assoc($kontakty))
				echo "<tr><td class=\"nadpis\">Kontakt:</td><td>".$kontakt["jmeno"].", Tel: ".$kontakt["telefon"]."</td></tr>";
			echo "<tr><td colspan=\"2\"><h2>Soubory ke stažení</h2></td></tr>";
			$pocet = 0;
			@$prilohy = mysql_query("SELECT * FROM vybRizeniPrilohy WHERE id_vr='".$_GET["id"]."' and id_verze='".$_GET["verze"]."'");
			while ($priloha = @mysql_fetch_assoc($prilohy)) {
				$pocet = $priloha["cislo"];
				$pripona = substr($priloha["popis"],strpos($priloha["popis"],".")+1);
				echo "<tr><td colspan=\"2\"><a href=\"$server/prodejWeb.php?soubor=".$priloha["id_vr"]."-".$priloha["id_verze"]."-".$priloha["cislo"]."\"><img src=\"$server/img/$pripona.gif\" alt=\"\" border=\"0\"> $pocet. ".$priloha["popis"]."</a> (".$priloha["velikost"]."kB)</td></tr>";
			}
			echo "</table>";
		}
	?>
	<br>
	<form name="razeni" action="<?php echo $server; ?>/prodejWeb.php" method="post">
	<input type="hidden" name="archiv" value="<?php echo $_POST["archiv"];?>" />
	<input type="submit" name="akce" value="<?php echo ($_POST["archiv"]==1 or (isset($_GET["id"]) and $radek["lhuta"]<date("Y-m-d H:i")))?"Zpìt":"Archiv"; ?>" /><br /><br />
	</form>
	
	<?php
	if ($_POST["archiv"]==0)
		$podminka="lhuta>=NOW()";
	else
		$podminka="lhuta<NOW()"; 
	if (isset($_GET["id"]))
		@$result = mysql_query("SELECT * FROM vyberovaRizeni WHERE zverejnit<=NOW() and id_vr='".$_GET["id"]."' ORDER BY id_vr DESC,id_verze DESC");
	else
		@$result = mysql_query("SELECT VR.* FROM vyberovaRizeni VR join (SELECT id_vr, max(id_verze) as id_verze FROM vyberovaRizeni  WHERE zverejnit<=NOW() and $podminka GROUP BY id_vr) MAX on VR.id_vr=MAX.id_vr and VR.id_verze=MAX.id_verze WHERE zverejnit<=NOW() and $podminka ORDER BY id_vr DESC,id_verze DESC");
	$cisloRadku = 0;
	echo "<table class=\"rizeni\" cellspacing=\"0\">\n";
	echo "<tr class=\"zahlaviTabulky\"><td rowspan=\"2\">verze</td><td colspan=\"4\">Název zakázky</td></tr>\n";
	echo "<tr class=\"zahlaviTabulky\"><td>datum uveøejnìní</td><td>Lhùta pro nabídky</td><td>Posl.zmìna</td><td>Pøedp.hodnota</td></tr>\n";
	while ($radek = mysql_fetch_assoc($result)) { 
	  echo "<TR id='".$radek["id_vr"].str_pad($radek["id_verze"],3,"0",STR_PAD_LEFT)."'";
	  if ($cisloRadku%2==0)
	    echo " class=\"licha\">\n";
	  else
	    echo " class=\"suda\">\n";
	  echo "<td rowspan=\"2\">".$radek["id_verze"].(($radek["zruseno"])?" &nbsp;<img src=\"images/delete.png\" alt=\"\" border=\"0\" width=\"15\" height=\"15\">":"")."</td><TD class=\"tucne\" colspan=\"4\"><a href=\"$server/prodejWeb.php?id=".$radek["id_vr"]."&verze=".$radek["id_verze"]."\">".$radek["nazev"]."</a></TD>\n";
	  echo "</TR>\n";
	  echo "<TR";
	  if ($cisloRadku%2==0)
	    echo " class=\"licha\">\n";
	  else
	    echo " class=\"suda\">\n";
	  echo "<TD>".date("d.m.Y",strtotime($radek["zverejnit"]))."</TD>";
	  echo "<TD>".date("d.m.Y",strtotime($radek["lhuta"]))."</TD>";
	  echo "<TD>".date("d.m.Y G:i",strtotime($radek["zmena"]))."</TD>";
	  echo "<TD align=\"right\">".(($radek["skrytCenu"])?"Neuvedena":number_format($radek["cena"], 0, ',', ' ').",-Kè")."</TD>";
	  echo "</TR>\n";
	  if (!empty($radek["poznamka"])) {
		  echo "<TR";
		  if ($cisloRadku%2==0)
		    echo " class=\"licha\">\n";
		  else
		    echo " class=\"suda\">\n";
		  echo "<TD></TD>";
		  echo "<TD colspan=\"3\">".$radek["poznamka"]."</TD>";
		  echo "</TR>\n";
	  }
	  $cisloRadku += 1;
	}
	if ($cisloRadku == 0) 
	  echo "<TR><TD colspan=\"4\" align=\"center\"><br>Momentálnì neprobíhá žádné výbìrové øízení.</TD></TR>";
	echo "</table>\n";
	?>
</div>
<?php readfile("http://www.ssok.cz/pata.html"); ?>
