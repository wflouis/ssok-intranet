<?php
include "funkce/databaze.php"; 

$result = mysql_query("SELECT idSmlouvyPO 
 FROM smlouvy s WHERE idSmlouvyPO>0 and zverejnit and not zverejneno");

if (mysql_num_rows($result)>0) {
//	$client = new SoapClient('http://www.portalpo.cz/olomouc/ws_sml/server.php?wsdl', array('encoding'=>'windows-1250', "trace" => 1, "exceptions" => 1));
	$client = new SoapClient('https://verejnyportalpo.kr-olomoucky.cz/ws_sml/server.php?wsdl', array('encoding'=>'windows-1250', "trace" => 1, "exceptions" => 1));
	$id_user = "c3NvazowOThmNmJjZDQ2MjFkMzczY2FkZTRlODMyNjI3YjRmNg==";

	while($smlouva = mysql_fetch_assoc($result)) {
		$parametry = array(
			"id_user"=>$id_user,
			"id_smlouvy"=>$smlouva["idSmlouvyPO"]);
	
		$import = $client->get_stav($parametry);
	}	
	
//var_dump($import);
	if ($import->state == "OK" and $import->zverejneno == "true") {
			mysql_query("UPDATE smlouvy SET zverejneno=1, 
						rs_id='".$import->rs_id."', 
						rs_id_verze='".$import->rs_id_verze."', 
						rs_link='".$import->rs_link."', 
						datumUcinnosti='".$import->datum_ucinnosti."' 
				WHERE idSmlouvyPO='".$smlouva["idSmlouvyPO"]."'"); 
	}
}
?>