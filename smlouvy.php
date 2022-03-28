<?php 
define("MAX_VEL_PRILOHY", 19000000);
include "funkce/funkce.php"; 
include "databaze.php"; 

if (isset($_POST['tlacitko']) && $_POST["tlacitko"] == "Export") {
	include "funkce/export.php";
	exit;
}

if (!maPristup("S")) 
	exit;
if (maPristup("P",true))
	$zmeny="";
else
	$zmeny=" disabled";
$fakturace = $zmeny;
if (maPristup("F",true)) 
	$fakturace = "";
if (maPristup("Z",true)) {
	$zaruky="";
	if (isset($_POST['typSmlouvy']) && $_POST["typSmlouvy"] == 100)
		$zmeny="";
} else
	$zaruky=" disabled";

define("cesta","/share/smlouvy/");
$parametry = array("id_user"=>"c3NvazowOThmNmJjZDQ2MjFkMzczY2FkZTRlODMyNjI3YjRmNg=="); 

if (!empty($_GET)) {
	$_SESSION["prilohy"] = $_SESSION["smluvniPartneri"] = array();
	$_SESSION["strana"] = 1;
	$_SESSION["stredisko"] = "%";
}
if (!isset($_POST["typSmlouvy"]) and !isset($_POST["id_smlouvy"]) or $_POST["akce"]=="Nov� smlouva") {
	$_POST["typSmlouvy"] = 100;
}
if (isset($_POST["strana"]) and !empty($_POST["strana"])) 
	$_SESSION["strana"] = $_POST["strana"];
else
	if (!empty($_SESSION["strana"]))
		$_POST["strana"] = $_SESSION["strana"];
	else
		$_POST["strana"] = 1;
if (isset($_POST["stredisko"]) and !empty($_POST["stredisko"])) 
	$_SESSION["stredisko"] = $_POST["stredisko"];
else
	if (!empty($_SESSION["stredisko"]))
		$_POST["stredisko"] = $_SESSION["stredisko"];
	else
		$_POST["stredisko"] = "%";

if (empty($_POST["raditPodle"])) {
	$_POST["raditPodle"] = "cisloSmlouvyPom";
}
if (empty($_POST["raditZaruky"])) {
	$_POST["raditZaruky"] = "datumZarukyDo";
}
if (empty($_POST["id_smlouvy"])) {
	$_POST["id_smlouvy"] = 0;
}
if (empty($_SESSION["okno"]))
	$_SESSION["okno"] = "1";
if (!empty($_POST["okno"]))
	$_SESSION["okno"] = $_POST["okno"];

define("pocStran", 10);
define("pocRadku", 30);

$mimetypes = array(
    'doc'        => 'application/msword',
    'docx'        => 'application/msword',
    'pdf'        => 'application/pdf',
    'xls'        => 'application/vnd.ms-excel',
    'xlsx'        => 'application/vnd.ms-excel',
    'ppt'        => 'application/vnd.ms-powerpoint',
    'xhtml'        => 'application/xhtml+xml',
    'zip'        => 'application/zip',
    'mid'        => 'audio/midi',
    'midi'        => 'audio/midi',
    'mp3'        => 'audio/mpeg',
    'wav'        => 'audio/x-wav',
    'bmp'        => 'image/bmp',
    'gif'        => 'image/gif',
    'jpeg'        => 'image/jpeg',
    'jpg'        => 'image/jpeg',
    'png'        => 'image/png',
    'tif'        => 'image/tiff',
    'html'        => 'text/html',
    'htm'        => 'text/html',
    'txt'        => 'text/plain',
    'rtf'        => 'text/rtf',
    'mpeg'        => 'video/mpeg',
    'mpg'        => 'video/mpeg',
    'mov'        => 'video/quicktime',
    'avi'        => 'video/x-msvideo',
);

if (!isset($_POST["zmeny"]))
	$_POST["zmeny"] = 0;
if (!isset($_SESSION["smer"]))
	$_SESSION["smer"]=" asc";
	
if (isset($_FILES['soubor'])) {
	if ($_FILES['soubor']['size']>MAX_VEL_PRILOHY) 
		$chyba = "Chyba: Soubor p��lohy je p��li� velk�. Zmen�ete p�edev��m velikost obr�zk� v n�jak�m grafick�m editoru!!!";
	else { 
		if (is_uploaded_file($_FILES['soubor']['tmp_name']) && (move_uploaded_file($_FILES['soubor']['tmp_name'], ".".$_FILES['soubor']['tmp_name']))) { 
			$_SESSION["prilohy"][] = array("id_smlouvy" => $_POST["id_smlouvy"],
											"cislo" => 0,
											"name" => $_FILES['soubor']['name'],
										    "tmp_name" => $_FILES['soubor']['tmp_name'],
										    "size" => ceil($_FILES['soubor']['size']/1024),
											"smazat" => false);
		}; 
	}
}
if (isset($_POST['ico']) and !empty($_POST['ico'])) {
	@$result = mysqli_query($link, "SELECT id_partnera, concat(partneri.ico,' - ',partneri.nazev,', ',partneri.ulice,', ',partneri.psc,' ',partneri.mesto) as nazev FROM partneri WHERE ico='".$_POST["ico"]."' LIMIT 1");
	if ($radek = @mysqli_fetch_assoc($result)) {
		$_SESSION["smluvniPartneri"][] = array("id_smlouvy" => $_POST["id_smlouvy"],
										"cislo" => 0,
										"nazev" => $radek["nazev"],
										"idPartnera" => $radek["id_partnera"],
										"smazat" => false);
	}
}
if (isset($_POST["zrusPrilohu"]) and isset($_SESSION["prilohy"][$_POST["zrusPrilohu"]])) {
	if (is_file(".".$_SESSION["prilohy"][$_POST["zrusPrilohu"]]["tmp_name"])) {
		unlink(".".$_SESSION["prilohy"][$_POST["zrusPrilohu"]]["tmp_name"]);
		unset($_SESSION["prilohy"][$_POST["zrusPrilohu"]]);
	} else
		$_SESSION["prilohy"][$_POST["zrusPrilohu"]]["smazat"] = true;
}
if (isset($_POST["zrusPartnera"]) and isset($_SESSION["smluvniPartneri"][$_POST["zrusPartnera"]])) {
	if ($_SESSION["smluvniPartneri"][$_POST["zrusPartnera"]]["cislo"]==0) {
		unset($_SESSION["smluvniPartneri"][$_POST["zrusPartnera"]]);
	} else
		$_SESSION["smluvniPartneri"][$_POST["zrusPartnera"]]["smazat"] = true;
}
	
if (isset($_POST["akce"])) { 
	switch ($_POST["akce"]) {
		case "zru�it vazbu": $_POST["vazba"]=0; unset($_POST["akce"]); //mysqli_query($link, "UPDATE smlouvy SET vazba = '0' WHERE id_smlouvy='".$_POST["id_smlouvy"]."'");
			break;
		case "Ulo�it": 
			if (!empty($_POST["cisloSmlouvy"]) and (maPristup("P",true) || maPristup("Z",true) || maPristup("F",true))) {
				$_POST["datumUzavreni"] = DateCzEn($_POST["datumUzavreni"]);
				$_POST["datumOd"] = DateCzEn($_POST["datumOd"]);
				$_POST["datumDo"] = DateCzEn($_POST["datumDo"]);
				$_POST["kdy"] = DateCzEn($_POST["kdy"]);
				$_POST["upozornit"] = (isset($_POST["upozornit"]) and $_POST["upozornit"]=="on")?"1":"0";
				$_POST["koncept"] = (isset($_POST["koncept"]) and $_POST["koncept"]=="on")?"1":"0";
				$_POST["uhrazeno"] = DateCzEn($_POST["uhrazeno"]);
				$_POST["zverejnit"] = (isset($_POST["zverejnit"]) and $_POST["zverejnit"]=="on")?"1":"0";
				$_POST["datumVypovezeni"] = DateCzEn($_POST["datumVypovezeni"]);
				$_POST["datumUcinnosti"] = DateCzEn($_POST["datumUcinnosti"]);
				$cisloSmlouvy = explode("-",trim($_POST["cisloSmlouvy"]));
				$_POST["cisloSmlouvyPom"] = $cisloSmlouvy[0]."-".sprintf("%03s",$cisloSmlouvy[1])."-".$cisloSmlouvy[2]; 

				if (isset($_POST["uhrady"]))
					foreach($_POST["uhrady"] as $klic => $uhrada)
						$_POST["uhrady"][$klic] = DateCzEn($_POST["uhrady"][$klic]);

				if (maPristup("P",true) || $_POST["typSmlouvy"] == 100) {
					if (is_uploaded_file($_FILES['soubor']['tmp_name'])) {
						$_POST["soubor"] = $_POST["cisloSmlouvy"].strchr($_FILES['soubor']['name'],".");
						$_POST["velikost"] = $_FILES['soubor']['size']/1024;
		   				move_uploaded_file($_FILES['soubor']['tmp_name'], cesta.$_POST["soubor"]);
					}
					if (!empty($_POST["souvisejici"])) {
						if ($_POST["vazba"]>0)
							mysqli_query($link, "UPDATE smlouvy SET vazba = '".($_POST["vazba"])."' WHERE id_smlouvy='".$_POST["souvisejici"]."'");
						else {
							$result = mysqli_query($link, "SELECT vazba FROM smlouvy WHERE id_smlouvy='".$_POST["souvisejici"]."'");
							$radek = mysqli_fetch_assoc($result);
							if ($radek["vazba"]>0)
								$_POST["vazba"] = $radek["vazba"];
							else {
								$result = mysqli_query($link, "SELECT smlouvy FROM citace");
								$radek = mysqli_fetch_assoc($result);
								mysqli_query($link, "UPDATE citace SET smlouvy = '".($radek["smlouvy"]+1)."'");
								$_POST["vazba"] = $radek["smlouvy"]+1;
								mysqli_query($link, "UPDATE smlouvy SET vazba = '".($radek["smlouvy"]+1)."' WHERE id_smlouvy='".$_POST["souvisejici"]."'");
							}
						}
					} 
					if (empty($_POST["strediska"]))
						$_POST["strediska"][] = '1';
					if (empty($_POST["id_smlouvy"]) and !empty($zmeny) and $_POST["typSmlouvy"]<>100)
						$_POST["typSmlouvy"] = 100;
					uloz("smlouvy",$_POST,$_POST["id_smlouvy"]); 
				    if (isset($_POST["strediska"])) {
		  		       smaz("smlouvyStr","id_smlouvy",$_POST["id_smlouvy"]); 
					   foreach($_POST["strediska"] as $_POST["id_strediska"])
				   		  uloz("smlouvyStr",$_POST); 
					} 

					if (isset($_SESSION["prilohy"])) {
						foreach($_SESSION["prilohy"] as $value) {
							if ($value["cislo"]>0) {
								if ($value["smazat"]) {
									mysqli_query($link, "DELETE FROM smlouvyPrilohy WHERE id_smlouvy='".$value["id_smlouvy"]."' and cislo='".$value["cislo"]."'");
								}
							} else if (!empty($value["tmp_name"]) and file_exists(".".$value["tmp_name"])) { 
								$_POST["cislo"] = 0;
								$_POST["nazev"] = $value["name"];
								$_POST["velikost"] = round(filesize(".".$value["tmp_name"])/1000);
								uloz("smlouvyPrilohy",$_POST,$_POST["cislo"]);
								rename(".".$value["tmp_name"],"/share/smlouvy/".$_POST["id_smlouvy"]."-".$_POST["cislo"]);
							} 
						}
					}
					unset($_SESSION["prilohy"]);
					if (isset($_SESSION["smluvniPartneri"])) {
						foreach($_SESSION["smluvniPartneri"] as $value) {
							if ($value["cislo"]>0) {
								if ($value["smazat"]) {
									mysqli_query($link, "DELETE FROM smlouvyPartneri WHERE id_smlouvy='".$value["id_smlouvy"]."' and cislo='".$value["cislo"]."'");
								}
							} else { 
								if (!$value["smazat"]) {
									$_POST["cislo"] = 0;
									$_POST["nazev"] = $value["name"];
									$_POST["idPartnera"] = $value["idPartnera"];
									uloz("smlouvyPartneri",$_POST,$_POST["cislo"]);
								}
							} 
						}
					}
					unset($_SESSION["smluvniPartneri"]);
				}
				if (maPristup("P",true) || $_POST["typSmlouvy"] == 100 || maPristup("F",true)) {
				    if (isset($_POST["faktury"])) {
					   smaz("smlouvyFak","id_smlouvy",$_POST["id_smlouvy"]); 
					   foreach($_POST["faktury"] as $klic => $_POST["faktura"]) {
					   	  if (!empty($_POST["faktura"])) {
					   	  	$_POST["uhrazeno"] = $_POST["uhrady"][$klic];
				   		  	uloz("smlouvyFak",$_POST);
						  }  
					   }
					}
				}
			}
			break;
		case "Ulo�it z�ruku": 
			if (!empty($_POST["cisloSmlouvy"]) and !empty($_POST["id_smlouvy"]) and maPristup("Z",true)) {
				smaz("zaruky","id_smlouvy",$_POST["id_smlouvy"]); 
				smaz("kontroly","id_smlouvy",$_POST["id_smlouvy"]); 
				for($index=1;isset($_POST["predmetZaruky_$index"]);$index++) 
					if (!empty($_POST["predmetZaruky_$index"])) {
						$_POST["predmetZaruky"] = $_POST["predmetZaruky_$index"]; 
						$_POST["datumZarukyOd"] = DateCzEn($_POST["datumZarukyOd_$index"]);
						$_POST["datumZarukyDo"] = DateCzEn($_POST["datumZarukyDo_$index"]);
						uloz("zaruky",$_POST,$_POST["id_zaruky"]); 
						for($indexK=1;isset($_POST["datumKontroly_$index"."_$indexK"]);$indexK++) 
							if (DateCzEn($_POST["datumKontroly_$index"."_$indexK"])>"2000-01-01") {
								$_POST["vysledekKontroly"] = $_POST["vysledekKontroly_$index"."_$indexK"];
								$_POST["datumKontroly"] = DateCzEn($_POST["datumKontroly_$index"."_$indexK"]);
								$_POST["zavady"] = (isset($_POST["zavady_$index"."_$indexK"]) and $_POST["zavady_$index"."_$indexK"]=="on")?"1":"0";
								$_POST["datumOdstraneni"] = DateCzEn($_POST["datumOdstraneni_$index"."_$indexK"]);
								uloz("kontroly",$_POST); 
							}
						$_POST["id_zaruky"] = 0;
					}
			}
			break;
			//  pot�eba dod�lat !!!
		case "Smazat": mysqli_query($link, "UPDATE smlouvy set smazano=1, zmenil='".$_SESSION["id_jmeno"]."', zadano=NOW() WHERE id_smlouvy='".$_POST["id_smlouvy"]."'");
		case "Seradit": 
			if ($_SESSION["smer"]==" asc")
				$_SESSION["smer"]=" desc";
			else
				$_SESSION["smer"]=" asc";
		case "Naj�t": $_POST["strana"]=1;
		case "Nov� smlouva":
			$_POST = array ('stredisko' => $_POST["stredisko"],'typSmlouvy' => $_POST["typSmlouvy"],'najit' => $_POST["najit"],'rok' => $_POST["rok"],'raditPodle' => $_POST["raditPodle"],'raditZaruky' => $_POST["raditZaruky"],'zmeny' => $_POST["zmeny"],'strana' => $_POST["strana"],'platnostOd' => $_POST["platnostOd"],'platnostDo' => $_POST["platnostDo"]); 
			$_SESSION["prilohy"] = $_SESSION["smluvniPartneri"] = array();
			break;
		case "Zobrazit": 
			if (!isset($_POST["cislo"]))
				$_POST["cislo"]=0;
			$result = mysqli_query($link, "SELECT * FROM smlouvyPrilohy WHERE id_smlouvy='".$_POST["id_smlouvy"]."' and cislo='".$_POST["cislo"]."' LIMIT 1");
			if (mysqli_num_rows($result)>0 and $radek = mysqli_fetch_assoc($result)) {
				$soubor = (($radek["cislo"]==0)?$radek["nazev"]:$radek["id_smlouvy"]."-".$radek["cislo"]);
				if (is_file(cesta.$soubor)) {
					$Pripona = substr(strrchr($radek["nazev"],"."),1,3); 
			     	header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		    		header('Content-Description: File Transfer');
					header('Content-type: '.$mimetypes[$Pripona]);
					header("Content-Length: ".filesize(cesta.$soubor));
//					header("Content-disposition: inline; filename=\'".$radek["nazev"]."\'");
				    header('Content-Disposition: attachment; filename='.$radek["nazev"]);
					readfile(cesta.$soubor);
//					exit;
				}
			} 
			$_POST["id_smlouvy"] = "";
	}
}
if (!empty($_POST["id_smlouvy"])) {
	if (preg_match("/akce/",implode(array_keys($_POST)))) {
		$_SESSION["prilohy"] = $_SESSION["smluvniPartneri"] = array();
		$result = mysqli_query($link, "SELECT s.*, p.nazev, p.ulice, p.psc, p.mesto FROM smlouvy s left join partneri p on (s.ico=p.ico) WHERE id_smlouvy='".$_POST["id_smlouvy"]."' LIMIT 1");
		if (mysqli_num_rows($result)>0 and $radek = mysqli_fetch_assoc($result)) {
			DbToPOST($radek);
			$_POST["nazevPartnera"]=$_POST["nazev"].", ".$_POST["ulice"].", ".$_POST["psc"]." ".$_POST["mesto"];
		}
		@$result = mysqli_query($link, "SELECT * FROM smlouvyPrilohy WHERE id_smlouvy='".$_POST["id_smlouvy"]."'");
		while ($radek = @mysqli_fetch_assoc($result)) {
			$_SESSION["prilohy"][] = array( "id_smlouvy" => $radek["id_smlouvy"],
											"cislo" => $radek["cislo"],
											"name" => $radek["nazev"],
											"tmp_name" => "",
											"size" => $radek["velikost"],
											"smazat" => false);
		}
		@$result = mysqli_query($link, "SELECT id_smlouvy, cislo, idPartnera, concat(partneri.ico,' - ',partneri.nazev,', ',partneri.ulice,', ',partneri.psc,' ',partneri.mesto) as nazev FROM smlouvyPartneri join partneri  on idPartnera=id_partnera WHERE id_smlouvy='".$_POST["id_smlouvy"]."'");
		while ($radek = @mysqli_fetch_assoc($result)) {
			$_SESSION["smluvniPartneri"][] = array( "id_smlouvy" => $radek["id_smlouvy"],
											"cislo" => $radek["cislo"],
											"nazev" => $radek["nazev"],
											"idPartnera" => $radek["idPartnera"],
											"smazat" => false);
		}

		$_POST["datumUzavreni"] = DateEnCz($_POST["datumUzavreni"]);
		$_POST["datumOd"] = DateEnCz($_POST["datumOd"]);
		$_POST["datumDo"] = DateEnCz($_POST["datumDo"]);
		$_POST["kdy"] = DateEnCz($_POST["kdy"]);
		$_POST["uhrazeno"] = DateEnCz($_POST["uhrazeno"]);
		$_POST["datumVypovezeni"] = DateEnCz($_POST["datumVypovezeni"]);
		$_POST["datumUcinnosti"] = DateEnCz($_POST["datumUcinnosti"]);
	
		$_POST["strediska"] = array();
		$result = mysqli_query($link, "SELECT id_strediska FROM smlouvyStr WHERE id_smlouvy='".$_POST["id_smlouvy"]."'");
		while($radek = mysqli_fetch_assoc($result))
			$_POST["strediska"][]=$radek["id_strediska"];
	
		$_POST["faktury"] = $_POST["uhrady"] = array();
		$result = mysqli_query($link, "SELECT faktura, uhrazeno FROM smlouvyFak WHERE id_smlouvy='".$_POST["id_smlouvy"]."'");
		while($radek = mysqli_fetch_assoc($result)) {
			$_POST["faktury"][]=$radek["faktura"];
			$_POST["uhrady"][]=DateEnCz($radek["uhrazeno"]);
		}
				
		$result = mysqli_query($link, "SELECT * FROM zaruky WHERE id_smlouvy='".$_POST["id_smlouvy"]."'");
		while($radek = mysqli_fetch_assoc($result)) {
			$_POST["predmetZaruky_".$radek["id_zaruky"]] = $radek["predmetZaruky"];
	//		$_POST["predmetZaruky_".$radek["id_zaruky"]] = htmlspecialchars($radek["predmetZaruky"]);
			$_POST["datumZarukyOd_".$radek["id_zaruky"]] = DateEnCz($radek["datumZarukyOd"]);
			$_POST["datumZarukyDo_".$radek["id_zaruky"]] = DateEnCz($radek["datumZarukyDo"]);
		}
		$result = mysqli_query($link, "SELECT * FROM kontroly WHERE id_smlouvy='".$_POST["id_smlouvy"]."'");
		while($radek = mysqli_fetch_assoc($result)) {
			$_POST["vysledekKontroly_".$radek["id_zaruky"]."_".$radek["id_kontroly"]] = $radek["vysledekKontroly"];
	//		$_POST["vysledekKontroly_".$radek["id_zaruky"]."_".$radek["id_kontroly"]] = htmlspecialchars($radek["vysledekKontroly"]);
			$_POST["datumKontroly_".$radek["id_zaruky"]."_".$radek["id_kontroly"]] = DateEnCz($radek["datumKontroly"]);
			$_POST["zavady_".$radek["id_zaruky"]."_".$radek["id_kontroly"]] = $radek["zavady"];
			$_POST["datumOdstraneni_".$radek["id_zaruky"]."_".$radek["id_kontroly"]] = DateEnCz($radek["datumOdstraneni"]);
		}
	}
} else
	if (empty($_POST["koncept"]))
		$_POST["koncept"] = 1;

/*if (isset($_POST["uhrady"]))
	foreach($_POST["uhrady"] as $klic => $uhrada)
		$_POST["uhrady"][$klic] = DateEnCz($_POST["uhrady"][$klic]); */
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<meta http-equiv="cache-control" content="max-age=0" />
	<meta http-equiv="cache-control" content="no-cache" />
	<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
	<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
	<LINK href="intranet.css" type=text/css rel=stylesheet>
	<LINK href="registr.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="jquery.js"></script>
	<script language="JavaScript" src="smlouvy.js"></script>
	<?php echo "<style>";
		switch ($_SESSION["okno"]) {
			case "1": echo "	#oknoSeznam { display: block;}"; break;
			case "2": echo "	#oknoDetail { display: block;}"; break;
			case "3": echo "	#oknoZaruky { display: block;}"; break;
			case "4": echo "	#oknoChyby  { display: block;}"; break;
		}
		echo "</style>";	
	?>
</head>
<body>
<p class=N3>Registr smluv</p>
<?php
	include "funkce/detail.php"; 
?>
<div id="oknoSeznam">
	<form action="smlouvy2.php" method="post" name="formSeznam" id="formSeznam">
	<input type="hidden" name="raditPodle" value="<?php echo $_POST["raditPodle"];?>">
	<input type="hidden" name="raditZaruky" value="<?php echo $_POST["raditZaruky"];?>">
	<input type="hidden" name="id_smlouvy" value="<?php echo $_POST["id_smlouvy"];?>">
	<input type="hidden" name="okno" value="1">
	<input type="hidden" name="partneri" value="0">
	<input type="hidden" name="cislo">
	<input type="hidden" name="strana" value="1">
	<input type="hidden" name="tlacitko">
	<span class="levySl">Typ smlouvy:</span> 
	<select name="typSmlouvy" onChange="">
		<option value="%">v�echny smlouvy</option>
	<?php $result = mysqli_query($link, "SELECT * FROM typySmluv WHERE 1=1 ORDER BY id_typuSmlouvy");
		while ($radek = mysqli_fetch_assoc($result))
			echo "<option value=\"".$radek["id_typuSmlouvy"]."\" ".(($radek["id_typuSmlouvy"]==$_POST["typSmlouvy"])?" selected":"").">".$radek["popis"]."</option>";
	?>
	</select>
	st�edisko: <select name="stredisko" onChange="">
		<option value="%">v�echna st�ediska</option>
	<?php $result = mysqli_query($link, "SELECT * FROM seznam_str WHERE 1=1");
		while ($radek = mysqli_fetch_assoc($result))
			echo "<option value=\"".$radek["id_str"]."\"".(($radek["id_str"]==$_POST["stredisko"])?" selected":"").">".$radek["nazev"]."</option>";
	?>
	</select> 
	<br>
	<span class="levySl">Rok:</span> 
	<select name="rok" onChange="">
	<?php $rok = date("Y");
		if (!isset($_POST["rok"]))
			$_POST["rok"] = $rok;
		while($rok>='2002') {
			echo "<option value=\"$rok\" ".(($rok==$_POST["rok"])?" selected":"").">$rok</option>";
			$rok--;
		}
	?>
		<option value="%" <?php echo (($_POST["rok"]=="%")?"selected":""); ?>>v�e</option>
	</select> &nbsp;&nbsp;Platnost smlouvy od &nbsp;<input type="text" class="cislo" name="platnostOd" size="10" maxlength="10" value="<?php echo $_POST["platnostOd"];?>"> do <input type="text" class="cislo" name="platnostDo" size="10" maxlength="10" value="<?php echo $_POST["platnostDo"];?>"><br>
	<span class="levySl">Hledat v�raz:</span> <input class="enter" type="text" name="najit" size="20" maxlength="20" value="<?php echo (isset($_POST["najit"])?$_POST["najit"]:""); ?>" onChange="this.form.submit()"> <input type="submit" name="akce" value="Naj�t"> <input type="submit" name="tlacitko" value="Export"> <span class="pozn">(hled� v pol�ch smlouva, predmet, ico, nazev partnera)</span>
	</form><br>
	<table cellpadding="0" cellspacing="0"><tr><td>
	<?php 
		$podminka = "WHERE not smazano and typSmlouvy LIKE '".$_POST["typSmlouvy"]."'";
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

		$result = mysqli_query($link, "SELECT count(smlouvy.id_smlouvy) as pocet FROM smlouvy 
				join (select distinct id_smlouvy from smlouvyStr where id_strediska like '".$_POST["stredisko"]."' or  id_strediska in (select id_str from seznam_str where nadrazene='".$_POST["stredisko"]."')) str on str.id_smlouvy=smlouvy.id_smlouvy $podminka"); 
		$zaznam = mysqli_fetch_assoc($result); 
		$posledni = ceil($zaznam["pocet"]/pocRadku);
		switch ($_POST["akce"]) {
			case "zacatek": $_POST["strana"] = 1; break;
			case "vlevo": $_POST["strana"] = ceil($_POST["strana"]/pocStran)*pocStran-pocStran; break;
			case "vpravo": $_POST["strana"] = ceil($_POST["strana"]/pocStran)*pocStran+1; break;
			case "konec": $_POST["strana"] = $posledni; break;
			default:
		}
		?>
		<table id=posun align="right"><tr><td onClick="navigace()">Nalezeno 
		<?php 
		echo $zaznam["pocet"]." z�znam� - ";
		if ($_POST["strana"] > pocStran)
			echo "<img src=\"img\zacatek.gif\"> <img src=\"img\\vlevo.gif\">";
		$do = ceil($_POST["strana"]/pocStran)*pocStran;
		$od = $do - pocStran + 1;
		for ($i=$od;$i<=min($do,$posledni);$i++) 
			if ($i == $_POST["strana"])
				echo " <a class=aktivni href=\"#\">$i</a>";
			else
				echo " <a href=\"#\">$i</a>";
		if ($do < $posledni)
			echo " <img src=\"img\\vpravo.gif\"> <img src=\"img\konec.gif\">";
	?>
	</td></tr></table><br><br>
	<table id="seznam" cellpadding="3" cellspacing="0">
	<thead class=HlTab><td onClick="serad('cisloSmlouvyPom')" width="80">Smlouva 
	<?php 
		if ($_POST["raditPodle"]=="cisloSmlouvy")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
	    echo "</td><td onClick=\"serad('datumUzavreni')\">uzav�ena ";
		if ($_POST["raditPodle"]=="datumUzavreni")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
		echo "</td><td>p�edm�t smlouvy</td><td>cena</td><td>velikost</td></thead>";
		$Sql = "";
		if (isset($_POST["stredisko"])) {
			$Sql  = "SELECT smlouvy.*, 
					(select nazev from smlouvyPartneri join partneri on idPartnera=id_partnera where id_smlouvy=smlouvy.id_smlouvy order by cislo limit 1)  as nazev, 
					(select min(cislo) from smlouvyPrilohy where id_smlouvy=smlouvy.id_smlouvy ) as cislo, 
				    (select nazev from smlouvyPrilohy where id_smlouvy=smlouvy.id_smlouvy order by cislo limit 1 ) as nazevSouboru,
				    (select velikost from smlouvyPrilohy where id_smlouvy=smlouvy.id_smlouvy order by cislo limit 1 ) as velikost 
				FROM smlouvy 
				join (select distinct id_smlouvy from smlouvyStr where id_strediska like '".$_POST["stredisko"]."' or  id_strediska in (select id_str from seznam_str where nadrazene='".$_POST["stredisko"]."')) str on str.id_smlouvy=smlouvy.id_smlouvy ";
			$Sql .= $podminka;
			$Sql .= " ORDER BY ".$_POST["raditPodle"]."".$_SESSION["smer"];
			$Sql .= " LIMIT ".(($_POST["strana"]-1)*pocRadku).",".pocRadku."";
		}
		$seznam=mysqli_query($link, $Sql); //echo $Sql; 
		while($radek = @mysqli_fetch_assoc($seznam)) {
			echo "<TR id=\"s".$radek["id_smlouvy"]."\"";
			echo " class=\"suda\">";
			echo "<td>".$radek["cisloSmlouvy"]."</td><td>".DateEnCz($radek["datumUzavreni"])."</td> <td>".$radek["predmet"]."</td><td>".$radek["cena"]."</td><td><a href=\"#\" id=\"z".$radek["id_smlouvy"]."-".$radek["cislo"]."\" class=\"zobrazit\">".((!is_null($radek["cislo"]))?"<img src=\"img/".strtolower(substr(strrchr($radek["nazevSouboru"],"."),1)).".gif\" border=\"0\"> ".$radek["velikost"]." kB":"")."</a></td>";
			echo "</tr>\n";
			echo "<tr class=\"doplnek\"><td></td><td colspan=\"4\">Sml.strana: ".$radek["nazev"]." Platnost: ".$radek["datumTxt"].(($radek["datumOd"]>"1990-01-01")?" od ".DateEnCz($radek["datumOd"]):"").(($radek["datumDo"]>"1990-01-01")?" do ".DateEnCz($radek["datumDo"]):"")."</td></tr>\n";
		} 
	?>
	</table>
	</td></tr></table>
</div>
</body>
</html>
