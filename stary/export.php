<?php
if (!maPristup()) 
	exit;

header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-type: text/csv");
header("Content-Description: File Transfer");
header("Content-Disposition: attachment; filename=export.csv");

$separator = ',';
$podminka = "typSmlouvy LIKE '".$_POST["typSmlouvy"]."'";
if ($_POST["rok"]!='%')
	$podminka .= " and datumUzavreni between '".$_POST["rok"]."-1-1' and '".$_POST["rok"]."-12-31'";
if (!empty($_POST["platnostOd"]))
	$podminka .= " and (datumOd = '0000-00-00' and datumUzavreni >= '".DateCzEn($_POST["platnostOd"])."' or datumOd >= '".DateCzEn($_POST["platnostOd"])."')";
if (!empty($_POST["platnostDo"]))
	$podminka .= " and datumDo <= '".DateCzEn($_POST["platnostDo"])."'";
if (!empty($_POST["najit"])) {
	$podminka .= " and (cisloSmlouvy LIKE '%".$_POST["najit"]."%' ";
	$podminka .= "or predmet LIKE '%".$_POST["najit"]."%' ";
	$podminka .= "or smlouvy.rodneCislo LIKE '%".$_POST["najit"]."%' ";
	$podminka .= "or exists (SELECT * FROM smlouvyPartneri join partneri on smlouvyPartneri.idPartnera=partneri.id_partnera WHERE smlouvyPartneri.id_smlouvy=smlouvy.id_smlouvy and (nazev LIKE '%".$_POST["najit"]."%' or ico LIKE '%".$_POST["najit"]."%'))) ";
} 
$query = "SELECT cisloSmlouvy,datumUzavreni,
	(select ico from smlouvyPartneri join partneri on idPartnera=id_partnera where id_smlouvy=smlouvy.id_smlouvy order by cislo limit 1)  as ico, 
	(select concat(nazev,', ',ulice,', ',psc,' ',mesto) from smlouvyPartneri join partneri on idPartnera=id_partnera where id_smlouvy=smlouvy.id_smlouvy order by cislo limit 1)  as nazev, 
	rodneCislo,predmet,cena,datumTxt,datumOd,datumDo,F.faktura,F.uhrazeno 
FROM smlouvy 
join (select distinct id_smlouvy from smlouvyStr where id_strediska like '".$_POST["stredisko"]."' or  id_strediska in (select id_str from seznam_str where nadrazene='".$_POST["stredisko"]."')) str on str.id_smlouvy=smlouvy.id_smlouvy
left join smlouvyFak F on F.id_smlouvy=smlouvy.id_smlouvy
WHERE $podminka"; 
$result = mysqli_query($_SESSION["link"],$query); 
if ($radek=mysqli_fetch_assoc($result)) {
	foreach($radek as $key => $value) 
		echo "\"".$key."\"".$separator;
	echo "\n";
	do {
		foreach($radek as $key => $value) 
			echo "\"".str_replace("\"","'",$value)."\"".$separator;
		echo "\n";
	} while ($radek=mysqli_fetch_assoc($result));
}
?>