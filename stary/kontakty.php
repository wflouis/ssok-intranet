<?php
session_start();
include "funkce/databaze.php"; 
readfile("http://www.ssok.cz/hlava.php?strana=kontakty&title=kontakty"); 
?>

<div class='hlavniOkno'>
<h3>Kontakní údaje dle jednotlivých støedisek a cestmistrovství</h3>
<p>Jednotlivé kontakty zobrazíte kliknutím na pøíslušné støedisko:</p>
<?php 
//<img src=\"http://intranet.ssok.cz/img/odrazka.jpg\" alt=\"\" border=\"0\">
echo "<h4 class=\"bezOdradkovani\"><a href=\"http://intranet.ssok.cz/kontakty.php?stredisko=SSOK#telefony\" title=\"Kontakty øeditelství SSOK\">Øeditelství SSOK</a></h4><br />";
@$result = mysql_query("SELECT * FROM seznam_str where poradi>0 and poradi!=1 ORDER BY poradi");
echo "<div class=\"odsazeniSeznamu\">";
$uroven = 0;
while ($radek = @mysql_fetch_assoc($result)) {
	if ($radek["nadrazene"]!=$uroven) {
		if ($radek["nadrazene"]>$uroven)
			echo "<div class=\"odsazeniSeznamu\">";
		else
			echo "</div>";
		$uroven = $radek["nadrazene"];
	}
	if ($radek["nadrazene"]==0)
		echo "<h5>";
	echo "<a href=\"http://intranet.ssok.cz/kontakty.php?stredisko=".$radek["zkratka"]."#telefony\" title=\"Kontakty ".$radek["nazev"]."\">".$radek["nazev"]."</a><br />";
	if ($radek["nadrazene"]==0)
		echo "</h5>";
}
echo "</div><br />";
if (isset($_GET["stredisko"])) {
	echo "<table id=\"telefony\" cellspacing=\"0\" cellpadding=\"0\">";
	@$result = mysql_query("SELECT str.*, nazev FROM strediska str, seznam_str sez where str.stredisko=sez.zkratka and sez.zkratka='".$_GET["stredisko"]."'");
	echo "<tr><td colspan=\"2\">";
	$prvni = true;
	while ($radek = mysql_fetch_assoc($result)) {
		if ($prvni) {
			echo "<h4>".$radek["nazev"]."</h4>";
			$prvni = false;
		}
		if (ereg("@",$radek["text"]))
			echo $radek["nadpis"]." <a href=\"mailto:".$radek["text"]."\">".$radek["text"]."</a><br />";
		else
			echo $radek["nadpis"]." ".$radek["text"]."<br />";
	}
	echo "<br /></td><td colspan=\"2\"></td></tr>";
	
	echo "<tr class='zahlaviTabulky'><td>Jméno</td><td>funkce</td><td width='80'>telefon</td><td>e-mail</td></tr>";
	@$result = mysql_query("SELECT * FROM seznam where stredisko='".$_GET["stredisko"]."' and internet = '1' ORDER BY poradi desc, jmeno");
	$cisloRadku = 0;
	while ($radek = mysql_fetch_assoc($result)) {
	  if ($cisloRadku%2==0)
	    echo "<TR  class=licha>\n";
	  else
	    echo "<TR  class=suda>\n";
	  echo "<TD>".$radek["jmeno"]."</TD>\n";
	  echo "<TD>".$radek["funkce"]."</TD>\n";
	  echo "<TD>".$radek["telefon"]."</TD>\n";
	  echo "<TD><a href=\"mailto:".$radek["email"]."\">".$radek["email"]."</a></TD>\n";
	  echo "</TR>\n";
	  $cisloRadku += 1;
	}
	echo "</table>\n";
}
echo "</div>";
readfile("http://www.ssok.cz/pata.html"); 
?>
