<?php
include "funkce/databaze.php";
header("Content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"windows-1250\"?>\n"; 
echo "<profil><profil_kod>60058933</profil_kod><zadavatel><ico_vlastni>70960399</ico_vlastni><nazev_zadavatele>Správa silnic Olomouckého kraje, pøíspìvková organizace</nazev_zadavatele></zadavatel>"; 

if (isset($_GET["od"]) and isset($_GET["do"])) {
	$od = date("Y-m-d",strtotime(substr_replace(substr_replace($_GET["od"],".",4,0),".",2,0)));
	//omezení rozmezí max na 1 rok od poèáteèního data
	$do = min(date("Y-m-d",strtotime("+12 month",strtotime($od))),date("Y-m-d",strtotime(substr_replace(substr_replace($_GET["do"],".",4,0),".",2,0))));
	$query = "SELECT CONCAT('P',SUBSTRING(zverejnit,3,2),'V',LPAD(VR.id_vr,8,'0')) as kod_vz_na_profilu, '' as kod_vz_na_usvzis, VR.nazev as nazev_vz, stavVZ as stav_vz, druhZR as druh_zadavaciho_rizeni,
			ico, U.nazev as nazev_uchazece, zeme, castkaBezDPH, castkaSDPH, dodavatel 
		FROM vyberovaRizeni VR 
			JOIN (SELECT id_vr, MAX(id_verze) as id_verze FROM vyberovaRizeni WHERE zmena BETWEEN '$od' AND '$do' GROUP BY id_vr) MAX ON VR.id_vr=MAX.id_vr and VR.id_verze=MAX.id_verze 
			LEFT JOIN vybRizeniUchazeci U ON VR.id_vr = U.id_vr
		WHERE zmena BETWEEN '$od' AND '$do' ORDER BY kod_vz_na_profilu, dodavatel";
	$result = @mysqli_query($_SESSION["link"],$query); 
	$aktKodProfilu = "";
	while($radek=@mysqli_fetch_assoc($result)) {
		if ($aktKodProfilu!=$radek["kod_vz_na_profilu"]) {
			if ($aktKodProfilu!="")
				echo "</zakazka>";
			$aktKodProfilu=$radek["kod_vz_na_profilu"];
			echo "<zakazka><vz><kod_vz_na_profilu>".$radek["kod_vz_na_profilu"]."</kod_vz_na_profilu><kod_vz_na_usvzis></kod_vz_na_usvzis><nazev_vz>".$radek["nazev_vz"]."</nazev_vz><stav_vz>".$radek["stav_vz"]."</stav_vz><druh_zadavaciho_rizeni>".$radek["druh_zadavaciho_rizeni"]."</druh_zadavaciho_rizeni></vz>";
		}
		if (!is_null($radek["ico"])) {
			echo "<uchazec><ico>".$radek["ico"]."</ico><nazev_uchazece>".$radek["nazev_uchazece"]."</nazev_uchazece><zeme_sidla>".$radek["zeme"]."</zeme_sidla><cena_s_dph>".$radek["castkaSDPH"]."</cena_s_dph></uchazec>";
			if ($radek["dodavatel"])
				echo "<dodavatel><ico>".$radek["ico"]."</ico><nazev_dodavatele>".$radek["nazev_uchazece"]."</nazev_dodavatele><zeme_sidla_dodavatele>".$radek["zeme"]."</zeme_sidla_dodavatele><cena_celkem_dle_smlouvy_DPH>".$radek["castkaSDPH"]."</cena_celkem_dle_smlouvy_DPH><cena_celkem_dle_smlouvy_bez_DPH>".$radek["castkaBezDPH"]."</cena_celkem_dle_smlouvy_bez_DPH></dodavatel>";
		}
	}
	if ($aktKodProfilu!="")
		echo "</zakazka>";
}
echo "</profil>\n";
?>