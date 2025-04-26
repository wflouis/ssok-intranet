<?php
ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);

include "funkce/databaze.php"; 
define("cesta","/share/smlouvy/");

$result = mysql_query("SELECT s.*, 
 (select hodnota from typyUdajuSmluv where typyUdajuSmluv.typSmlouvyPO = s.typSmlouvyPO and typ_udaje='stav' and s.stavPO=id_udaje) as stav,
 (select hodnota from typyUdajuSmluv where typyUdajuSmluv.typSmlouvyPO = s.typSmlouvyPO and typ_udaje='druh' and s.druhPO=id_udaje) as druh,
 (select hodnota from typyUdajuSmluv where typyUdajuSmluv.typSmlouvyPO = s.typSmlouvyPO and typ_udaje='typ' and s.typPO=id_udaje) as typ,
 (select hodnota from typyUdajuSmluv where typyUdajuSmluv.typSmlouvyPO = s.typSmlouvyPO and typ_udaje='pozice' and s.pozicePO=id_udaje) as pozice
 FROM smlouvy s WHERE not koncept and typSmlouvyPO>0 and idSmlouvyPO is null");

if (mysql_num_rows($result)>0) {
//	$client = new SoapClient('http://www.portalpo.cz/olomouc/ws_sml/server.php?wsdl', array('encoding'=>'windows-1250', "trace" => 1, "exceptions" => 1));
	$client = new SoapClient('https://verejnyportalpo.kr-olomoucky.cz/ws_sml/server.php?wsdl', array('encoding'=>'windows-1250', "trace" => 1, "exceptions" => 1));
	$id_user = "c3NvazowOThmNmJjZDQ2MjFkMzczY2FkZTRlODMyNjI3YjRmNg==";
 
	while($smlouva = mysql_fetch_assoc($result)) {
		$parametry = array(
			"id_user"=>$id_user,
			"koncept"=>"false");
			
		switch ($smlouva["typSmlouvyPO"]) {
		case "jiné":
			$parametry = array_merge( $parametry, array(
				"cislo"=>$smlouva["cisloSmlouvyPom"],
				"nazev"=>$smlouva["predmet"],
				"stav"=>$smlouva["stav"],
				"druh"=>$smlouva["druh"],
				"typ"=>$smlouva["typ"]));
			break;
		case "nájemní":
			$parametry = array_merge( $parametry, array(
				"cislo"=>$smlouva["cisloSmlouvyPom"],
				"nazev"=>$smlouva["predmet"],
				"po_pozice"=>$smlouva["pozice"],
				"stav"=>$smlouva["stav"],
				"druh"=>$smlouva["druh"],
				"podtyp"=>$smlouva["typ"]));
			break;
		case "dary":
			$parametry = array_merge( $parametry, array(
				"cislo"=>$smlouva["cisloSmlouvyPom"],
				"nazev"=>$smlouva["predmet"],
				"druh"=>$smlouva["druh"]));
			break;
		case "energie":
			$parametry = array_merge( $parametry, array(
				"cislo"=>$smlouva["cisloSmlouvyPom"],
				"nazev"=>$smlouva["predmet"],
				"ramcova"=>""));
			break;
		case "dílèí":
			$parametry = array_merge( $parametry, array(
				"ramcova"=>$smlouva["ramcovaPO"],
				"stav"=>$smlouva["stav"]));
			break;
		}
		$partneri["smluvni_strany"] = array();
		$dotaz = mysql_query("SELECT partneri.ico, partneri.nazev, concat(partneri.ulice,', ',partneri.psc,' ',partneri.mesto) as adresa FROM smlouvyPartneri join partneri  on idPartnera=id_partnera WHERE id_smlouvy='".$smlouva["id_smlouvy"]."'");
		while ($radek = @mysql_fetch_assoc($dotaz)) {
			$partneri["smluvni_strany"][] = array("nazev"=>$radek["nazev"],
						 	     				  "adresa"=>$radek["adresa"],
							     				  "ico"=>$radek["ico"]);
		}
		$parametry = array_merge( $parametry, $partneri);
	
	/*
				"nemovitosti"=> array("0"=> array("nem_katastr_nazev"=>"Olomouc",
							"nem_pozemky"=>"",
							"nem_podnikani"=>"",
							"nem_parcel_cislo"=>"",
							"nem_vymera"=>"",
							"nem_vymera_pronaj"=>"",
							"nem_cis_popis"=>"",
							"nem_pravni_vztah"=>"",
							"nem_poznamka"=>"",
							"nem_cis_popis"=>"")
					),
	*/
		
		switch ($smlouva["typSmlouvyPO"]) {
		case "jiné":
			$parametry = array_merge( $parametry, array(
				"dt_uzavreni"=>$smlouva["datumUzavreni"],
				"dt_ucinnosti"=>"",
				"neurcita"=>"",
				"dt_ukonceni"=>"",
				"cena_typ"=>$smlouva["typCeny"],
				"cena_bez_dph"=>"",
				"cena_s_dph"=>"",
				"mena"=>"CZK",
				"predmet"=>$smlouva["predmet"],
				"zverejnit_vz"=>($smlouva["zverejnit"]?"true":"false"),
				"sanon"=>"",
				"usneseni"=>""
				));
			break;
		case "nájemní":
			$parametry = array_merge( $parametry, array(
				"dt_uzavreni"=>$smlouva["datumUzavreni"],
				"dt_uzivani_od"=>"",
				"dt_uzivani_do"=>"",
				"neurcita"=>"",
				"dt_ukonceni"=>"",
				"cena_typ"=>$smlouva["typCeny"],
				"cena_bez_dph"=>"",
				"cena_s_dph"=>"",
				"mena"=>"CZK",
				"predmet"=>$smlouva["predmet"],
				"nem_predmet_dane"=>"false",
				"nem_ucinna_zo"=>"",
				"nemovitosti"=> array(),
				"cena_registr"=>"",
				"zverejnit_vz"=>($smlouva["zverejnit"]?"true":"false"),
				"sanon"=>"",
				"usneseni"=>""
				));
			break;
		case "dary":
			$parametry = array_merge( $parametry, array(
				"dt_uzavreni"=>$smlouva["datumUzavreni"],
				"dt_dar"=>"",
				"hodnota_dar"=>"",
				"mena"=>"CZK",
				"predmet"=>$smlouva["predmet"],
				"dar_vlast_po"=>"",
				"usneseni"=>""
				));
			break;
		case "energie":
			$parametry = array_merge( $parametry, array(
				"druh_energie"=>$smlouva["druh"],
				"stav"=>$smlouva["stav"],
				"podtyp"=>$smlouva["typ"],
				"dt_vypovezeni"=>$smlouva["datumVypovezeni"],
				"vypovezeno"=> ($smlouva["vypovezeno"]?"true":"false"),
				"dt_uzavreni"=>$smlouva["datumUzavreni"],
				"dt_ucinnosti"=>"",
				"neurcita"=>"",
				"dt_ukonceni"=>"",
				"cena_typ"=>$smlouva["typCeny"],
				"cena_bez_dph"=>"",
				"cena_s_dph"=>"",
				"mena"=>"CZK",
				"predmet"=>$smlouva["predmet"],
				"zverejnit_vz"=>($smlouva["zverejnit"]?"true":"false"),
				"sanon"=>""
				));
			break;
		case "dílèí":
			$parametry = array_merge( $parametry, array(
				"dt_uzavreni"=>$smlouva["datumUzavreni"],
				"dt_ucinnosti"=>"",
				"neurcita"=>"",
				"dt_ukonceni"=>"",
				"cena_typ"=>$smlouva["typCeny"],
				"cena_bez_dph"=>"",
				"cena_s_dph"=>"",
				"mena"=>"CZK",
				"predmet"=>$smlouva["predmet"],
				"zverejnit_vz"=>($smlouva["zverejnit"]?"true":"false"),
				"sanon"=>""
				));
			break;
		}
	
		$prilohy["soubory"] = array();
		$dotaz = mysql_query("SELECT id_smlouvy, cislo, nazev FROM smlouvyPrilohy WHERE id_smlouvy='".$smlouva["id_smlouvy"]."'");
		while ($radek = @mysql_fetch_assoc($dotaz)) {
			$contents = "";
			$soubor = cesta.(($radek["cislo"]==0)?$radek["nazev"]:$radek["id_smlouvy"]."-".$radek["cislo"]);
			if (is_file($soubor)) {
				$handle = fopen($soubor, "rb");
				$contents = fread($handle, filesize($soubor));
				fclose($handle);
			}
			$prilohy["soubory"][] = array("nazev"=>$radek["nazev"],
					      				  "base_64_obsah"=>base64_encode($contents));
		}
		$parametry = array_merge( $parametry, $prilohy);


try { 
		switch ($smlouva["typSmlouvyPO"]) {
		case "jiné":
			$import = $client->importJine($parametry);
			break;
		case "nájemní":
			$import = $client->importNajemni($parametry);
			break;
		case "dary":
			$import = $client->importDary($parametry);
			break;
		case "energie":
			$import = $client->importEnergie($parametry);
			break;
		case "dílèí":
			$import = $client->importDilci($parametry);
			break;
		}	
} catch (SoapFault $import) { 
    echo $import->getMessage(); 
} 
		echo "<br><br>";
//		var_dump($parametry);
		var_dump($import);
		if (false and $import->state == "OK") {
			mysql_query("UPDATE smlouvy SET idSmlouvyPO='".$import->id_smlouvy."' WHERE id_smlouvy='".$smlouva["id_smlouvy"]."'"); 
			mysql_query("DELETE FROM smlouvyChyby WHERE id_smlouvy = '".$smlouva["id_smlouvy"]."'"); 
		} else {
			mysql_query("DELETE FROM smlouvyChyby WHERE id_smlouvy = '".$smlouva["id_smlouvy"]."'"); 
			mysql_query("INSERT INTO smlouvyChyby VALUES( '".$smlouva["id_smlouvy"]."','".strtr($import->message,array("Element"=>"Údaj","soubory"=>"pøílohy"))."')"); 
		}
	}
}
?>