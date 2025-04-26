<?php
ini_set('default_charset', 'windows-1250');
session_start();

function maPristup($modul="X",$castecny=false) {
	$_SESSION["ip"] = vratIP(); 
	if (isset($_SESSION["prava"]) && !(strpos($_SESSION["prava"], $modul)=== false)) {
		$_SESSION["parametryMajetku"] = array ('typMajetku', 'majetek', 'parametr', 'hodnota');
		$_SESSION["typyMajetku"] = array ('id_typuMajetku', 'popis');
		$_SESSION["majetek"] = array ('id_majetku', 'invCislo', 'typMajetku', 'stredisko', 'mistnost', 'popis');
		$_SESSION["smlouvy"] = array ('id_smlouvy', 'typSmlouvy', 'cisloSmlouvy', 'cisloSmlouvyPom', 'typSmlouvyPO','koncept', 'ramcovaPO', 'stavPO', 'typPO', 'druhPO', 'pozicePO','datumUzavreni', 'datumVypovezeni', 'vypovezeno', 'ico', 'rodneCislo', 'smluvniStrana', 'predmet', 'typCeny', 'cena', 'datumTxt', 'datumOd', 'datumDo','soubor', 'velikost', 'upozornit', 'kdy', 'text', 'uhrazeno', 'faktura', 'vazba', 'zverejnit', 'zadano', 'zadal','zmenil','smazano');
		$_SESSION["smlouvyNemovitosti"] = array ('id_smlouvy', 'id_nemovitosti', 'nazevKatastru');
		$_SESSION["smlouvyPartneri"] = array ('id_smlouvy', 'id_poradi', 'idPartnera', 'zmena', 'zadal');
		$_SESSION["smlouvyPrilohy"] = array ('id_smlouvy', 'cislo', 'nazev', 'velikost', 'zmena', 'zadal');
		$_SESSION["smernice"] = array ('id_smernice', 'cislo', 'revize', 'nazev', 'plat_od', 'plat_do', 'poznamka', 'soubor', 'archiv');
		$_SESSION["zaruky"] = array ('id_smlouvy', 'id_zaruky', 'predmetZaruky', 'datumZarukyOd', 'datumZarukyDo', 'zadal');
		$_SESSION["kontroly"] = array ('id_smlouvy', 'id_zaruky', 'id_kontroly', 'vysledekKontroly', 'datumKontroly', 'zavady', 'datumOdstraneni');
		$_SESSION["partneri"] = array ('id_partnera', 'ico', 'nazev', 'ulice', 'psc', 'mesto', 'osoba', 'kadresa', 'telefon', 'email');
		$_SESSION["vyb_rizeni"] = array ('id_vr', 'cj', 'datum', 'datumStazeni', 'kontakt', 'predmet', 'termin', 'poznamka', 'priloha', 'archiv');
		$_SESSION["vyberovaRizeni"] = array ('id_vr', 'id_verze', 'kod_vz_uver','nazev', 'druh', 'popis', 'cena', 'skrytCenu', 'druhZR', 'stavVZ', 'cpv', 'lhuta', 'zverejnit', 'poznamka', 'archivovat', 'zruseno', 'zadal');
		$_SESSION["vybRizeniUchazeci"] = array ('id_vr', 'poradi', 'ico', 'nazev', 'zeme', 'castkaBezDPH', 'castkaSDPH', 'dodavatel', 'zadal');
		$_SESSION["vybRizeniPrilohy"] = array ('id_vr', 'id_verze', 'cislo', 'velikost', 'popis');
		$_SESSION["vybRizeniFakturace"] = array ('id_vr', 'rok', 'cenaBezDPH', 'cenaSDPH','zadal');
		$_SESSION["seznam"] = array ('id_jmeno', 'jmeno', 'funkce', 'telefon', 'mobil', 'stredisko', 'email', 'kod', 'ip', 'heslo', 'internet');
		$_SESSION["seznam_str"] = array ('id_str', 'zkratka', 'nazev', 'poradi');
		$_SESSION["pristPrava"] = array ('id_jmeno', 'id_modulu', 'prava');
		$_SESSION["smlouvyStr"] = array ('id_smlouvy', 'id_strediska');
		$_SESSION["smlouvyFak"] = array ('id_smlouvy', 'faktura', 'uhrazeno');
		$_SESSION["akce"] = array ('id_akce', 'nazev', 'cislo_silnice', 'dodavatel', 'usek', 'osoba', 'telefon', 'zadano', 'id_zadal', 'zmenil', 'stav');
		$_SESSION["citace"] = array ('smlouvy');
		$_SESSION["akce_polozky"] = array ('id_akce', 'id_polozky', 'predmet', 'nedodelky', 'prevzeti', 'zaruka', 'konec_zaruky', 'overil', 'stav');
		$_POST["zadano"] = date('Y-m-d H:i:s',time());
		$_SESSION["spravce"] = "<a href=\"mailto: ulmann@scomeq.cz\">správce aplikace</a>";
		return true;
	} 
	if ($castecny) 
		return false;
	if (isset($_SESSION["prava"]))
		Header("Location: zamitnuti.html");
	else
		Header("Location: over.php");
	return false;
}

function uloz($tabulka, &$data, &$klic=0) { 
   if ($klic > 0) { 
	   $sloupce  = "";
	   $podminka = "";
	   if ($tabulka=="smlouvy")
	      $_POST["zmenil"] = $_SESSION["id_jmeno"];
	   else 
	      $_POST["zadal"] = $_SESSION["id_jmeno"];
	   foreach ($data as $index=>$hodnota) 
   		  if (array_search($index,$_SESSION[$tabulka])>-1) 
			 if (substr($index,0,3)=='id_')
 	       	 	$podminka  .= "and ".$index."='".$hodnota."' " ;
			 else
	         	$sloupce  .= ",".$index."='".$hodnota."'" ;
	   $query = "UPDATE ".$tabulka." SET ".substr($sloupce,1)." WHERE ".substr($podminka,3)."";
	   $result = mysqli_query($_SESSION["link"],$query); 
   } else { 
	   $hodnoty = "";
	   $sloupce = "";
	   $_POST["zadal"] = $_SESSION["id_jmeno"];
	   foreach ($data as $index=>$hodnota) 
  		  if (array_search($index,$_SESSION[$tabulka])>-1) {
	    	 $sloupce .= ",".$index ;
		 	 $hodnoty .= "','".$hodnota ;
	  	  } 
	   $query   = "INSERT INTO ".$tabulka." (".substr($sloupce,1).") VALUES ('".substr($hodnoty,3)."')";
	   $result = mysqli_query($_SESSION["link"],$query);
	   $klic = mysqli_insert_id($_SESSION["link"]);
   } 
   if ($result == null) {
 	  echo "<br>Nepodaøilo se uložit záznam!!!<br>".$query."<br>";
	  return false;
   } //echo $query;
   return true;
}

function smaz($tabulka, $pole, $klic, $limit=0) { 
   $query  =  "DELETE FROM ".$tabulka." WHERE ".$pole." = '".$klic."'";
   if ($limit>0)
	   $query  .= " LIMIT $limit";
   $result = mysqli_query($_SESSION["link"],$query);
   if ($result = 0) {
 	  echo "<br>Nepodaøilo se uložit záznam!!!<br>".$query."<br>";
	  return false;
   }
   return true;
}

function povoleny($file,$dir) {
	if ($file == "." || $file == "..")
		return false;
	if (is_dir($dir."/".$file))
        return true;
	$pripony = array("doc","dot","xls","xlsx","docx","jpg", "gif", "tif", "txt","zip","pdf","htm","html","avi","pps","ppt","pptx");
    foreach ($pripony as $pripona) {
           if (preg_match("/\.". $pripona ."$/i", $file, $matches)) {
               return true;
           }
    }
	return false;
}
function obrazek($soubor) {
	if (is_dir($soubor))
		return "dir";
	else
		return strtolower(trim(substr($soubor,strrpos($soubor,".")+1,4)));
}
function obsahAdr($dir,$podle,$smer,$najit="",$selekce="") {
	global $i,$Nazev,$Cesta,$Zmena,$Velikost;
	if (!isset($i)) {
		$Nazev = $Cesta = $Zmena = $Velikost = array();
		$i=0;
	}
    if (is_dir($dir)) {
		$handle=opendir($dir);
		while (false!==($file = readdir($handle))) {
		   if (povoleny($file,$dir)) {
		   	   if (!empty($najit) and is_dir($dir."/".$file)) 
				   obsahAdr($dir."/".$file,"","nepis",$najit); 
		  	   if (empty($najit) or preg_match("/".$najit."/i",$file,$matches)) {
			       $Nazev[$i] = $file;
			       $Zmena[$i] = date("Y-m-d",filemtime($dir."/".$file));
				   if (is_file($dir."/".$file)) {
				       $Velikost[$i] = filesize($dir."/".$file);
					   $Cesta[$i] = $dir."/";
				   } else
					   $Velikost[$i] = "";
				   $i += 1;
			   }
		   }
		}
		closedir($handle); 
		if ($smer == "nepis")
			return;
		if ($podle == 1) {
			natcasesort($Nazev);
			if ($smer == ' asc')
				$Serazene= $Nazev;
			else
				$Serazene= array_reverse($Nazev,true);
		} else {
			natcasesort($Zmena);
			if ($smer == ' asc')
				$Serazene= $Zmena;
			else
				$Serazene= array_reverse($Zmena,true);
		}
		$i=0;
		if ((substr($dir,0,8) == "/share/R" && substr_count($dir,"/")>2) || substr_count($dir,"/")>3) {
			   echo "<tr><td>";
			   echo "<img src=\"img/updir.gif\" border=\"0\"> ";
			   echo "..</td><td></td><td align=\"right\"></td></tr>\n";
		  	   $i += 1;
		}

		foreach ($Serazene as $key => $value) { 
			if (strrpos(strrchr($dir, "/"),"(dle strediska)")==false || empty($_SESSION["dleStrediska"]) || strpos($Nazev[$key],$_SESSION["dleStrediska"]) || strpos($Nazev[$key],"reditelstvi")) {		
			    if ($i%2==0)
				   echo "<tr><td name=\"".(isset($Cesta[$i])?$Cesta[$i]:"")."\">";
				else
				   echo "<tr class=suda><td name=\"".(isset($Cesta[$i])?$Cesta[$i]:"")."\">";
				if ($podle == 1) {
				    echo "<img src=\"img/".obrazek($dir."/".$value).".gif\" border=\"0\"> "; 
				    echo "$value</td><td>".DateEnCz($Zmena[$key])."</td><td align=\"right\">".$Velikost[$key]."</td></tr>\n";
				} else {
				    echo "<img src=\"img/".obrazek($dir."/".$Nazev[$key]).".gif\" border=\"0\"> ";
				    echo $Nazev[$key]."</td><td>".DateEnCz($value)."</td><td align=\"right\">".$Velikost[$key]."</td></tr>\n";
				}
		  	    $i += 1;
			}
		}
 	} 
}	
function vratIP() {
	$RIp = array();
    if  (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strpos($_SERVER['HTTP_X_FORWARDED_FOR'],',')) {
        $RIp +=  explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);
    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $RIp[] = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    $RIp[] = $_SERVER['REMOTE_ADDR'];
    return $RIp;
}
Function DateCzEn($datum) {
	$text=explode(".",trim($datum));
	return $text[2]."-".$text[1]."-".$text[0]; 
}
Function DateEnCz($datum) {
	if (empty($datum)) 
		return "";
	$text=explode("-",trim($datum));
	return $text[2].".".$text[1].".".$text[0]; 
}

Function GoMonth($datum,$mesicu) {
	$text=explode(".",trim($datum));
	$konec = mktime (0,0,0,$text[1]+$mesicu+1,1,$text[2])-86400;
	return date ("Y-m-d", mktime (0,0,0,date("m",$konec),min($text[0],date("d",$konec)),date("Y",$konec)));
}

function doplnVyhlRet($retezec) {
	if (strchr($retezec,"%") == "" && strchr($retezec,"_") == "")
		return ("%".$retezec."%");
	else
		return $retezec;
}

function DbToPOST(&$radek) {
	foreach($radek as $klic => $hodnota) 
		$_POST[$klic] = $hodnota;
}
function getDBSet($tabulka,$pole) {
    $sql  = "SHOW COLUMNS FROM $tabulka LIKE '$pole'";
	$ret  = mysqli_query($_SESSION["link"],$sql);
    $line = mysqli_fetch_assoc($ret);
    $set  = $line['Type'];
    $set  = substr($set,5,strlen($set)-7); 
    return preg_split("/','/",$set); 
}
?>
