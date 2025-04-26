<?php
function maPristup() {
	@$RIp = vratIP();
	session_start();
	if (isset($_SESSION["vstup"])) {
		return true;
	}
	Header("Location: index.php");
	return false;
}
function srovnejKody($hodnota) {
	global $link;
	$vypis = mysqli_query($link, "SELECT kod FROM pristup WHERE kod = '$hodnota' LIMIT 1");
	if (mysqli_num_rows($vypis) == 0) 
		return false;
	else
		return true;
}
function vratIP() {
     if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
            return  $_SERVER["HTTP_X_FORWARDED_FOR"];  
     }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) { 
            return $_SERVER["REMOTE_ADDR"]; 
     }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            return $_SERVER["HTTP_CLIENT_IP"]; 
     } 

     return '';
}
Function DateCzEn($datum) {
	$text=explode(".",trim($datum));
	return $text[2]."-".$text[1]."-".$text[0]; 
}
Function DateEnCz($datum) {
	$text=explode("-",trim($datum));
	return $text[2].".".$text[1].".".$text[0]; 
}
function doplnVyhlRet($retezec) {
	if (strchr($retezec,"%") == "" && strchr($retezec,"_") == "")
		return ("%".$retezec."%");
	else
		return $retezec;
}
function vytvorDotaz($typ, $neniPole) {
	$pole = "";
	switch ($typ) {
	case "pole":
		foreach ($_POST as $klic => $hodnota)
			if (!in_array($klic,$neniPole))
				$pole .= "$klic,";
				break;
	case "value":
		foreach ($_POST as $klic => $hodnota)
			if (!in_array($klic,$neniPole))
				$pole .= "'".$hodnota."',";
				break;
	case "update":
		foreach ($_POST as $klic => $hodnota)
			if (!in_array($klic,$neniPole))
				$pole .= "$klic = '".$hodnota."',";
	}
	$pole = substr($pole,0,strlen($pole)-1);
	return $pole;
}
function vyprazdni() {
	foreach ($_POST as $i => $value) 
		$_POST[$i] == "1";
	$_POST["rok"] = date("y");
}
function akceSDbf($akce,$dbf,$neniPole,$nKlice,$klic,$nKlice1 = 1,$klic1 = 1,$nKlice2 = 1,$klic2 = 1) {
	global $link;
	$Chyba = "";
	switch ($akce) {
		case "smazat": 
			if (!empty($klic)) {
				$Sql  = "DELETE FROM $dbf WHERE $nKlice = '$klic' AND $nKlice1 = '$klic1' AND $nKlice2 = '$klic2' LIMIT 1";
				$result = mysqli_query($link, $Sql);
				if (mysqli_affected_rows($link) == 0)
					$Chyba = "smazat!";
			}
		case "":
		case "novy"  : 
			Vyprazdni();
			break;
		case "stornovat": 
			if (!empty($klic)) {
				$Sql  = "UPDATE $dbf SET storno = '".$_POST["storno"]."' ";
				$Sql .= "WHERE $nKlice = '$klic' AND $nKlice1 = '$klic1' AND $nKlice2 = '$klic2' LIMIT 1";
				$result=mysqli_query($link, $Sql);
				if (mysqli_affected_rows($link) == 0)
					$Chyba = "stornovat!";
			}
			break;
		case "ulozit": 
			if (empty($klic)) {
				$Sql  = "INSERT INTO $dbf (".vytvorDotaz("pole",$neniPole).")";
				$Sql .= "VALUES(".vytvorDotaz("value",$neniPole).")"; 
				$result = mysqli_query($link, "$Sql");
				if (mysqli_affected_rows($link) == 0)
						$Chyba = "vložit!";
				else
					$klic = mysqli_insert_id();
				Vyprazdni();
				break;
			} else {
				$Sql  = "UPDATE $dbf SET ".vytvorDotaz("update",$neniPole)." "; 
				$Sql .= "WHERE $nKlice = '$klic' AND $nKlice1 = '$klic1' AND $nKlice2 = '$klic2' LIMIT 1";
				$result = mysqli_query($link, "$Sql");
				if (mysqli_error() != "")
					$Chyba = "opravit!";
			}
		case "nacti" : 
			if (!empty($klic)) {
				$Sql  = "SELECT * FROM $dbf ";
				$Sql .= "WHERE $nKlice = '$klic' AND $nKlice1 = '$klic1' AND $nKlice2 = '$klic2' LIMIT 1";

				$result=mysqli_query($link, $Sql);
				if (mysqli_num_rows($result) == 0)
					$Chyba = "najít!";
				else {
					$zaznam = mysqli_fetch_assoc($result);
					foreach ($zaznam as $pole => $hodnota) 
						$_POST[$pole] = $hodnota;
				}
			}
	} 
	if (!empty($Chyba)) {
		echo "Chyba: Záznam nelze $Chyba";
	}
}
?>
