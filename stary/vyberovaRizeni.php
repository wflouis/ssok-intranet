<?php
define("MAX_VEL_PRILOHY", 40000000);
include "funkce/funkce.php"; 
if (!maPristup("V")) 
	exit;

if (isset($_GET["soubor"]) && !empty($_GET["soubor"]) && file_exists("documents/".basename($_GET["soubor"]))) { 
	Header("Location: documents/".basename($_GET["soubor"]));
	exit;
}
$_POST["kontakt"]=$_SESSION['id_jmeno'];

include "funkce/databaze.php"; 
$result = mysqli_query($_SESSION["link"],"SELECT * FROM seznam WHERE id_jmeno='".$_POST["kontakt"]."'");
$radek = mysqli_fetch_assoc($result);
$chyba = "";
$_SESSION["zadavajici"] = $radek["jmeno"].", ".$radek["funkce"].", ".$radek["telefon"];

if (isset($_GET["poradi"]) or isset($_GET["rok"])) {
	$_POST["id_vr"] = $_GET["id_vr"];
	$_POST["poradi"] = $_GET["poradi"];
	$_POST["rok"] = $_GET["rok"];
}
if (!isset($_POST["id_vr"])) {
	$_POST["akce"] = "Nový záznam";
	unset($_SESSION["prilohy"]);
}
if (isset($_GET["hledat"])) 
	$_SESSION["hledat"] = $_GET["hledat"];
if (!isset($_POST["archiv"])) 
	$_POST["archiv"] = $_SESSION["archiv"];
	
$_POST["cena"] = preg_replace(array('/[^0-9\,\.]/','/\,/'),array('','.'),$_POST["cena"]);
$_POST["zverejnit"] = DateCzEn($_POST["zverejnit_date"])." ".$_POST["zverejnit_time"];
$_POST["lhuta"] = DateCzEn($_POST["lhuta_date"])." ".$_POST["lhuta_time"];
$_POST["skrytCenu"] = isset($_POST["skrytCenu"]);
$_POST["dodavatel"] = isset($_POST["dodavatel"]);
$_POST["archivovat"] = isset($_POST["archivovat"]);
if ($_FILES['soubor']['size']>MAX_VEL_PRILOHY) 
	$chyba = "Chyba: Soubor pøílohy je pøíliš velký. Zmenšete pøedevším velikost obrázkù v nìjakém grafickém editoru!!!";
else { echo $_FILES['soubor']['tmp_name'];
	if (!empty($_FILES['soubor']) && ($_FILES['soubor']['size']>0) && (move_uploaded_file($_FILES['soubor']['tmp_name'], "./tmp/".$_FILES['soubor']['name']))) 
	  { 
	    $_SESSION["prilohy"][] = $_FILES['soubor']['name'];
	  }; 
}

switch ($_POST["akce"]) {
	case "Uložit": 
		if (!empty($_POST["nazev"]) and $_POST["zverejnit"]>'2011-01-01') {
			if (!$_POST["zruseno"]) {
				if (!empty($_POST["id_vr"])) {
					$result = mysqli_query($_SESSION["link"],"SELECT zverejnit, lhuta, max(id_verze) as id_verze FROM vyberovaRizeni WHERE id_vr='".$_POST["id_vr"]."' GROUP BY id_vr");
					if ($radek = mysqli_fetch_assoc($result)) {
						if (true || $radek["lhuta"]>date("Y-m-d H:i:s")) { //pùvodnì nevytváøelo novou verzi v archivu, ale nešlo uložit zmìny v textu
							if ($radek["zverejnit"]<date("Y-m-d H:i:s")) {
								$_POST["zverejnit"] = $radek["zverejnit"];
								$_POST["id_verze"] = $radek["id_verze"]+1;
								uloz("vyberovaRizeni",$_POST);
								mysqli_query($_SESSION["link"],"INSERT INTO vybRizeniPrilohy
									SELECT id_vr , '".$_POST["id_verze"]."' AS id_verze , cislo, IFNULL(kopieVerze,id_verze) as kopieVerze, velikost , popis
										FROM vybRizeniPrilohy
										WHERE id_vr='".$_POST["id_vr"]."' and id_verze='".($_POST["id_verze"]-1)."'");
							} else
								uloz("vyberovaRizeni",$_POST,$_POST["id_vr"]);
						} else {
							$zmeny = array();
							$zmeny["id_vr"] = $_POST["id_vr"];
							$zmeny["id_verze"] = $_POST["id_verze"];
							$zmeny["zadal"] = $_POST["zadal"];
							uloz("vyberovaRizeni",$zmeny,$_POST["id_vr"]);
						}
					}
				} else
					uloz("vyberovaRizeni",$_POST,$_POST["id_vr"]);
				foreach($_SESSION["prilohy"] as $value) {
					if (file_exists("./tmp/$value")) { 
						$_POST["cislo"] = 0;
						$_POST["popis"] = $value;
						$_POST["velikost"] = round(filesize("./tmp/$value")/1000);
						uloz("vybRizeniPrilohy",$_POST,$_POST["cislo"]);
						rename("./tmp/$value","/profilzadavatele/profilzadavssl/rizeni/".$_POST["id_vr"]."-".$_POST["id_verze"]."-".$_POST["cislo"]);
					} else
						if (ereg("-",$value)) { 
							$priloha = explode("-",$value); 
							$result = mysqli_query($_SESSION["link"],"SELECT kopieVerze FROM vybRizeniPrilohy WHERE id_vr='".$_POST["id_vr"]."' and id_verze='".$_POST["id_verze"]."' and cislo='".$priloha[1]."' LIMIT 1");
							if ($radek = mysqli_fetch_assoc($result)){
								if (is_null($radek["kopieVerze"]))
									unlink("/profilzadavatele/profilzadavssl/rizeni/".$_POST["id_vr"]."-".$_POST["id_verze"]."-".$priloha[1]);
								mysqli_query($_SESSION["link"],"DELETE FROM vybRizeniPrilohy WHERE id_vr='".$_POST["id_vr"]."' and id_verze='".$_POST["id_verze"]."' and cislo='".$priloha[1]."' LIMIT 1");
							}
						}
				}
				$_SESSION["prilohy"] = array();
			} else
				$chyba = "Zrušený záznam nelze editovat!!!";
		} else
			$chyba = "Chyba: Neúplné zadání, zkontrolujte a zkuste znovu!!!";
		break;
	case "Zrušit": 
		if (!empty($_POST["id_vr"]))
			if ($_POST["zverejnit"]<$_POST["zadano"])
				mysqli_query($_SESSION["link"],"UPDATE vyberovaRizeni SET zruseno = '1' WHERE id_vr = '".$_POST["id_vr"]."'"); 
			else {
				mysqli_query($_SESSION["link"],"DELETE FROM vyberovaRizeni WHERE id_vr = '".$_POST["id_vr"]."'"); 
				mysqli_query($_SESSION["link"],"DELETE FROM vybRizeniPrilohy WHERE id_vr = '".$_POST["id_vr"]."'"); 
			}
		break;
	case "Zpìt":
	case "Archiv": $_POST["archiv"]=($_POST["archiv"]+1)%2; $_SESSION["archiv"] = $_POST["archiv"];
	case "- Zpìt -":
	case "Nový záznam":
		$_POST = array ("archiv" => $_POST["archiv"],"zverejnit" => date("Y-m-d G:i",mktime(0, 0, 0, date("m"), date("d")+1, date("Y"))),"archivovat" => "1"); break;
	default: 
		if (ereg("-",$_POST["zrusPrilohu"])) { 
			$_SESSION["prilohy"][] = $_POST["zrusPrilohu"];
		} else
			if (is_numeric($_POST["zrusPrilohu"])) {
				unlink("./tmp/".$_SESSION["prilohy"][$_POST["zrusPrilohu"]]);
				unset($_SESSION["prilohy"][$_POST["zrusPrilohu"]]);
			}
}
$_POST["id_verze"] = max(1,$_POST["id_verze"]); 
if (!empty($_POST["id_vr"])) {
	switch ($_POST["akce_u"]) { 
		case "Uložit":	
			if (!empty($_POST["nazev"]) and !empty($_POST["ico"])) {
				$_POST["castkaBezDPH"] = preg_replace(array('/[^0-9\,\.]/','/\,/'),array('','.'),$_POST["castkaBezDPH"]);
				$_POST["castkaSDPH"] = preg_replace(array('/[^0-9\,\.]/','/\,/'),array('','.'),$_POST["castkaSDPH"]);
				uloz("vybRizeniUchazeci",$_POST,$_POST["poradi"]); 
			}
			if (!empty($_POST["rok"])) { 
				$_POST["cenaBezDPH"] = preg_replace(array('/[^0-9\,\.]/','/\,/'),array('','.'),$_POST["cenaBezDPH"]);
				$_POST["cenaSDPH"] = preg_replace(array('/[^0-9\,\.]/','/\,/'),array('','.'),$_POST["cenaSDPH"]);
				uloz("vybRizeniFakturace",$_POST,$_POST["update"]); 
				unset($_POST["rok"]);
				unset($_POST["cenaBezDPH"]);
				unset($_POST["cenaSDPH"]);
			}
			break;
		case "Zrušit":  
			if (!empty($_POST["poradi"]));
				mysqli_query($_SESSION["link"],"DELETE FROM vybRizeniUchazeci WHERE id_vr = '".$_POST["id_vr"]."' and poradi='".$_POST["poradi"]."'"); 
			if (!empty($_POST["rok"]));
				mysqli_query($_SESSION["link"],"DELETE FROM vybRizeniFakturace WHERE id_vr = '".$_POST["id_vr"]."' and rok='".$_POST["rok"]."'"); 
		case "Nový uchazeè": $_POST["poradi"] = "";
	}
	if (empty($_FILES['soubor']['name']) and !is_numeric($_POST["zrusPrilohu"]) and !ereg("-",$_POST["zrusPrilohu"])) {
		$query   = "SELECT *, (druh|0) as druh_dec, (druhZR|0) as druhZR_dec, (stavVZ|0) as stavVZ_dec FROM vyberovaRizeni WHERE id_vr='".$_POST["id_vr"]."' and id_verze='".$_POST["id_verze"]."' LIMIT 1";
		$result = mysqli_query($_SESSION["link"],$query);
		if (mysqli_num_rows($result)>0 and $radek = mysqli_fetch_assoc($result)) {
			DbToPOST($radek);
			$_POST["druh"] = $_POST["druh_dec"];
			$_POST["druhZR"] = $_POST["druhZR_dec"];
			$_POST["stavVZ"] = $_POST["stavVZ_dec"];
		}
	}
}
if (empty($_POST["archiv"]))
	$_POST["archiv"]=0;
$termin = explode(" ",$_POST["zverejnit"]);
$_POST["zverejnit_date"] = DateEnCz($termin[0]);
$_POST["zverejnit_time"] = substr($termin[1],0,5);
$termin = explode(" ",$_POST["lhuta"]);
$_POST["lhuta_date"] = DateEnCz($termin[0]);
$_POST["lhuta_time"] = substr($termin[1],0,5);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
	<LINK href="rizeni.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="jquery.js"></script>
	<script language="JavaScript" src="funkce.js"></script>
	<script language="JavaScript" src="vyberovaRizeni.js"></script>
	<?php
	if ($_POST["archiv"]==0)
	   echo "<LINK href=\"rizeni.css\" type=text/css rel=stylesheet>\n";
	?>
</head>
<body>
<p class=N3>Výbìrová øízení<?php echo ($_POST["archiv"]==1)?" - archiv":""; ?></p>
<?php echo "<div class=chyba>".$chyba."</div>";?>
<div class=form1>
	<form name="razeni" id="razeni" action="vyberovaRizeni.php" method="post" ENCTYPE="multipart/form-data">
	<input type="hidden" name="id_vr" value="<?php echo $_POST["id_vr"];?>">
	<input type="hidden" name="archiv" value="<?php echo ($_POST["archiv"]==1 or (isset($_POST["id_vr"]) and $_POST["lhuta"]<date("Y-m-d H:i")))?"1":"0"; ?>" />
	<input type="hidden" name="id_verze" value="<?php echo $_POST["id_verze"];?>">
	<input type="hidden" name="zruseno" value="<?php echo $_POST["zruseno"];?>">
	<input type="hidden" name="zrusPrilohu" id="zrusPrilohu">
		<span class="levySl">Zadávající:</span><span class="txPole"><?php echo $_SESSION["zadavajici"];?></span><br>
		<span class="levySl">Odkaz na profil:</span><span class="txPole"><?php echo (empty($_POST["id_vr"]))?"":"https://profilzadavatele.ssok.cz/index.php?id=".$_POST["id_vr"];?></span><br>
		<span class="levySl">Název:</span> <input type="text" name="nazev" size="70" maxlength="150" value="<?php echo $_POST["nazev"];?>"><br>
		<span class="levySl">Veøejná zakázka na:</span> 
		<select name="druh">
			<?php
			$nabidka = getDBSet("vyberovaRizeni","druh"); print_r($nabidka);
			echo "<option value=\"0\" ".((is_null($_POST["druh"]))?"SELECTED":"")."></option>\n";
			foreach($nabidka as $key => $value)
				echo "<option value=\"".pow(2,$key)."\" ".(($_POST["druh"]==pow(2,$key))?"SELECTED":"").">$value</option>\n";
			?>
		</select> Kód VZ z vìstníku: <input type="text" name="kod_vz_uver" size="10" maxlength="8" value="<?php echo $_POST["kod_vz_uver"];?>"><br>
		<span class="levySl">Popis:</span> <textarea cols="50" rows="4" name="popis"><?php echo $_POST["popis"];?></textarea><br>
		<span class="levySl">Cena (bez DPH):</span> <input class="cislo" type="text" name="cena" size="10" maxlength="10" value="<?php echo $_POST["cena"];?>">
			&nbsp;&nbsp;Skrýt cenu: <input type="checkbox" name="skrytCenu" <?php echo (($_POST["skrytCenu"])?"checked":""); ?>> <br>
		<span class="levySl">Druh zadávacího øízení:</span> 
		<select name="druhZR">
			<?php
			$nabidka = getDBSet("vyberovaRizeni","druhZR");
			echo "<option value=\"0\" ".(($_POST["druhZR"]==0)?"SELECTED":"")."></option>\n";
			foreach($nabidka as $key => $value)
				echo "<option value=\"".pow(2,$key)."\" ".(($_POST["druhZR"]==pow(2,$key))?"SELECTED":"").">$value</option>\n";
			?>
		</select><br>
		<span class="levySl">Datum uveøejnìní:</span> <input class="datum" type="text" name="zverejnit_date" size="10" maxlength="10" value="<?php echo $_POST["zverejnit_date"];?>"> v <input type="text" name="zverejnit_time" size="5" maxlength="5" value="<?php echo $_POST["zverejnit_time"];?>"><br>
		<span class="levySl">Lhùta pro podání nabídek:</span> <input class="datum" type="text" name="lhuta_date" size="10" maxlength="10" value="<?php echo $_POST["lhuta_date"];?>"> v <input type="text" name="lhuta_time" size="5" maxlength="5" value="<?php echo $_POST["lhuta_time"];?>"><br>
		<?php
			@$result = mysqli_query($_SESSION["link"],"SELECT text FROM cpvKody WHERE kod='".$_POST["cpv"]."'");
			if ($radek=@mysqli_fetch_assoc($result)) 
				$text = $radek["text"];
		?>
		<span class="levySl">CPV:</span> <input type="text" name="cpv" size="10" maxlength="10" value="<?php echo $_POST["cpv"];?>" onChange="doplnUdaje(this.value)">	<span id="text" class="popisek"> <?php echo $text; ?></span><br> 
		<span class="levySl">Stav:</span> 
		<select name="stavVZ">
			<?php
			$nabidka = getDBSet("vyberovaRizeni","stavVZ");
			echo "<option value=\"0\" ".(($_POST["stavVZ"]==0)?"SELECTED":"")."></option>\n";
			foreach($nabidka as $key => $value)
				echo "<option value=\"".pow(2,$key)."\" ".(($_POST["stavVZ"]==pow(2,$key))?"SELECTED":"").">$value</option>\n";
			?>
		</select><br>
		<span class="levySl">Poznámka:</span> <textarea cols="50" rows="2" name="poznamka"><?php echo $_POST["poznamka"];?></textarea><br>
	
		<span class="levySl">Zobrazovat v archivu:</span><input type="checkbox" name="archivovat" <?php echo (($_POST["archivovat"])?"checked":""); ?>> <br><br>
		
		<span class="levySl">Pøílohy:</span> 
		<?php
			$pocet = 0;
			@$result = mysqli_query($_SESSION["link"],"SELECT * FROM vybRizeniPrilohy WHERE id_vr='".$_POST["id_vr"]."' and id_verze='".$_POST["id_verze"]."'");
			while ($radek = @mysqli_fetch_assoc($result)) {
				$pocet++;;
				echo "<br><span class=\"levySl\"></span><a href=\"prodejWeb.php?soubor=".$radek["id_vr"]."-".(is_null($radek["kopieVerze"])?$radek["id_verze"]:$radek["kopieVerze"])."-".$radek["cislo"]."\">$pocet. ".$radek["popis"]."</a> &nbsp;&nbsp;<img src=\"img/delete.png\" alt=\"smazat pøílohu\" width=\"10\" height=\"10\" border=\"0\" onClick=\"smazPrilohu('".$radek["id_verze"]."-".$radek["cislo"]."');\">";
			}
			foreach($_SESSION["prilohy"] as $key => $value)
				echo "<br><span class=\"levySl\"></span>".($pocet+$key+1).((ereg("-",$value))?". po uložení se smaže pøíloha":".")." $value &nbsp;&nbsp;<img src=\"img/delete.png\" alt=\"smazat pžílohu\" width=\"10\" height=\"10\" border=\"0\" onClick=\"smazPrilohu('$key');\">";
		?>
		<br><span class="levySl"></span><input type="file" name="soubor" size="40" onChange="this.form.submit()"> <span class="popisek">(max.14MB)</span><br><br>
		<span class="levySl"></span><input class="tlacitko" type="submit" name="akce" value="Uložit"> <input type="submit" name="akce" value="Nový záznam"> <input type="submit" name="akce" value="Zrušit">
		<input type="submit" name="akce" value="<?php echo ($_POST["archiv"]==1 or (isset($_POST["id_vr"]) and $_POST["lhuta"]<date("Y-m-d H:i")))?((isset($_POST["id_vr"]))?"- Zpìt -":"Zpìt"):"Archiv"; ?>" /></div>
	</form><br>
	<form name="uchazeci" id="uchazeci" action="vyberovaRizeni.php" method="post">
		<input type="hidden" name="id_vr" value="<?php echo $_POST["id_vr"];?>">
		<input type="hidden" name="poradi" value="<?php echo $_POST["poradi"];?>">
		<input type="hidden" name="update" value="<?php echo (isset($_GET["rok"])?"1":"0");?>">
		<?php
			if (isset($_POST["poradi"]) and !empty($_POST["poradi"])) {
				$pocet = 0;
				@$result = mysqli_query($_SESSION["link"],"SELECT * FROM vybRizeniUchazeci WHERE id_vr='".$_POST["id_vr"]."' and poradi='".$_POST["poradi"]."'");
				$radek = @mysqli_fetch_assoc($result);
			}
		?>
		<h3>Uchazeèi:</h3>
		<span class="levySl">Název:</span> <input type="text" name="nazev" size="70" maxlength="150" value="<?php echo $radek["nazev"];?>"><br>
		<span class="levySl">IÈO:</span> <input type="text" name="ico" size="10" maxlength="10" value="<?php echo $radek["ico"];?>"> Zemì: <input type="text" name="zeme" size="3" maxlength="3" value="<?php echo $radek["zeme"];?>"> Dodavatel: <input type="checkbox" name="dodavatel" <?php echo (($radek["dodavatel"])?"checked":""); ?>><br>
		<span class="levySl">Èástka bez DPH:</span> <input class="cislo" type="text" name="castkaBezDPH" size="10" maxlength="10" value="<?php echo $radek["castkaBezDPH"];?>"> Èástka s DPH: <input type="text" class="cislo" name="castkaSDPH" size="10" maxlength="10" value="<?php echo $radek["castkaSDPH"];?>"> <br>
		<?php
			$pocet = 0;
			@$result = mysqli_query($_SESSION["link"],"SELECT * FROM vybRizeniUchazeci WHERE id_vr='".$_POST["id_vr"]."'");
			while ($radek = @mysqli_fetch_assoc($result)) {
				$pocet++;
				echo "<br><span class=\"levySl\"></span><strong><a href=\"vyberovaRizeni.php?id_vr=".$_POST["id_vr"]."&poradi=".$radek["poradi"]."\">".$radek["ico"]." ".$radek["nazev"]."</a></strong> (".$radek["zeme"].")";
				echo "<br><span class=\"levySl\"></span>".number_format($radek["castkaBezDPH"], 2, ',', ' ').",-Kè bez DPH, ".number_format($radek["castkaSDPH"], 2, ',', ' ').",-Kè s DPH";
			}
		?> <br>
		<?php
			if (isset($_POST["rok"]) and !empty($_POST["rok"])) {
				$pocet = 0;
				@$result = mysqli_query($_SESSION["link"],"SELECT * FROM vybRizeniFakturace WHERE id_vr='".$_POST["id_vr"]."' and rok='".$_POST["rok"]."'");
				$radek = @mysqli_fetch_assoc($result);
			}
		?>
		<span class="levySl">Skuteènì uhrazeno:</span> rok: <input class="cislo" type="text" name="rok" size="4" maxlength="4" value="<?php echo $radek["rok"];?>"> Èástka bez DPH: <input class="cislo" type="text" name="cenaBezDPH" size="10" maxlength="15" value="<?php echo $radek["cenaBezDPH"];?>"> Èástka s DPH: <input type="text" class="cislo" name="cenaSDPH" size="10" maxlength="15" value="<?php echo $radek["cenaSDPH"];?>"><br>
		<?php
			@$result = mysqli_query($_SESSION["link"],"SELECT * FROM vybRizeniFakturace WHERE id_vr='".$_POST["id_vr"]."' ORDER BY rok");
			while ($radek = @mysqli_fetch_assoc($result)) {
				echo "<br><span class=\"levySl\"></span><strong><a href=\"vyberovaRizeni.php?id_vr=".$_POST["id_vr"]."&rok=".$radek["rok"]."\">rok: ".$radek["rok"]." uhrazeno: ".number_format($radek["cenaBezDPH"], 2, ',', ' ').",-Kè bez DPH, ".number_format($radek["cenaSDPH"], 2, ',', ' ').",-Kè s DPH</a></strong>";
			}
		?> <br>
		<span class="levySl"></span><input class="tlacitko" type="submit" name="akce_u" value="Uložit"> <input type="submit" name="akce_u" value="Nový uchazeè"> <input type="submit" name="akce_u" value="Zrušit">
	</form><br>
	<form>
		<span class="levySl"><strong>Filtr podle názvu:</strong></span> <input type="text" id="hledat" name="hledat" size="30" maxlength="50" value="<?php echo $_SESSION["hledat"];?>"> <input type="button" name="zobrazit" value="Hledej" onClick="location.href='vyberovaRizeni.php?hledat='+document.getElementById('hledat').value"><br><br>
	</form>		
</div>
<div>
<?php
if ($_POST["archiv"]==0)
	$podminka="lhuta>=NOW()";
else
	$podminka="lhuta<NOW()"; 
@$result = mysqli_query($_SESSION["link"],"SELECT * FROM vyberovaRizeni WHERE $podminka and nazev like '%".$_SESSION["hledat"]."%' ORDER BY id_vr DESC,id_verze DESC");
$cisloRadku = 0;
echo "<table class=\"rizeni\" cellspacing=\"0\">\n";
echo "<tr class=\"HlTab\"><td rowspan=\"2\">verze</td><td colspan=\"4\">Název zakázky</td></tr>\n";
echo "<tr class=\"HlTab\"><td>datum uveøejnìní</td><td>Lhùta pro nabídky</td><td>Posl.zmìna</td><td>Pøedp.hodnota</td></tr>\n";
while ($radek = mysqli_fetch_assoc($result)) { 
  echo "<TR id='".$radek["id_vr"].str_pad($radek["id_verze"],3,"0",STR_PAD_LEFT)."'";
  if ($cisloRadku%2==0)
    echo " class=\"licha\">\n";
  else
    echo " class=\"suda\">\n";
  echo "<td rowspan=\"2\">".$radek["id_verze"].(($radek["zruseno"])?" &nbsp;<img src=\"img/delete.png\" alt=\"\" border=\"0\" width=\"15\" height=\"15\">":"")."</td><TD class=\"popisek\" colspan=\"4\">".$radek["nazev"]."</TD>\n";
  echo "</TR>\n";
  echo "<TR";
  if ($cisloRadku%2==0)
    echo " class=\"licha\">\n";
  else
    echo " class=\"suda\">\n";
  echo "<TD>".date("d.m.Y",strtotime($radek["zverejnit"]))."</TD>";
  echo "<TD>".date("d.m.Y",strtotime($radek["lhuta"]))."</TD>";
  echo "<TD>".date("d.m.Y G:i",strtotime($radek["zmena"]))."</TD>";
  echo "<TD align=\"right\">".number_format($radek["cena"], 0, ',', ' ')."</TD>";
  echo "</TR>\n";
  $cisloRadku += 1;
}
if ($cisloRadku == 0) 
  echo "<TR><TD colspan=\"4\" align=\"center\"><br>Momentálnì neprobíhá žádné výbìrové øízení.</TD></TR>";
echo "</table>\n";
?>
</div>
</body>
</html>
