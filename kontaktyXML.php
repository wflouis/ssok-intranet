<?php
session_start();
include "funkce/databaze.php"; 
header('Expires: ' . gmdate('D, d M Y H:i:s') . '  GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . '  GMT');
header('Content-Type: text/xml; charset=windows-1250');
?>
<?xml version="1.0" encoding="utf-8"?>
<?php
//SELECT zkratka, nazev, IF(id_str=1,0,IF(id_str=21,1,hlavni))+nadrazene as nadrazene, (SELECT zkratka FROM `seznam_str` Z WHERE Z.id_str=IF(S.id_str=1,0,IF(S.id_str=21,1,S.hlavni))+S.nadrazene) as zkratkaNadrazeny FROM `seznam_str` S WHERE poradi>0 ORDER BY nadrazene, poradi
$result = @mysqli_query($_SESSION["link"],"SELECT *, IF(id_str=1,0,IF(hlavni=1 or id_str=21,1,2)) as uroven FROM seznam_str where poradi>0 ORDER BY poradi");
$strediska = array(); 
while ($stredisko = @mysqli_fetch_assoc($result)) {
	$strediska[] = $stredisko; 
}
$radek = 0;
stredisko($strediska,$radek,0);

function stredisko($strediska,&$radek,$uroven) {
	$kody = array("ssok","stredisko","cestmistrovstvi"); 
	while (isset($strediska[$radek]) and $uroven==$strediska[$radek]["uroven"]) {
		echo "<".$kody[$uroven].">\n";
		echo "<nazev>".$strediska[$radek]["nazev"]."</nazev>\n";
		$popisy = @mysqli_query($_SESSION["link"],"SELECT str.*, nazev FROM strediska str, seznam_str sez where str.stredisko=sez.zkratka and sez.zkratka='".$strediska[$radek]["zkratka"]."'");
		while ($popis = @mysqli_fetch_assoc($popisy)) {
			echo "<popis>\n";
				echo "<nadpis>".$popis["nadpis"]."</nadpis>\n";
				echo "<text>".$popis["text"]."</text>\n";
			echo "</popis>\n";
		}
		seznam($strediska[$radek]["zkratka"]);
		if ($strediska[$radek]["hlavni"]=="1") {
			$radek++;
			stredisko($strediska,$radek,$uroven+1);
			$radek--;
		}
		echo "</".$kody[$uroven].">\n";
		$radek++;
	} 
}

function seznam($stredisko) {
	echo "<seznam>\n";
	$kontakty = @mysqli_query($_SESSION["link"],"SELECT * FROM seznam where stredisko='$stredisko' and internet = '1' ORDER BY poradi desc, jmeno");
	while ($kontakt = mysqli_fetch_assoc($kontakty)) {
		echo "<kontakt>\n";
			echo "<jmeno>".$kontakt["jmeno"]."</jmeno>\n";
			echo "<funkce>".$kontakt["funkce"]."</funkce>\n";
			echo "<telefon>".$kontakt["telefon"]."</telefon>\n";
			echo "<mail>".$kontakt["email"]."</mail>\n";
		echo "</kontakt>\n";
	}
	echo "</seznam>\n";
}
?>

