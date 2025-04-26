<?php
include "funkce/funkce.php"; 
include "funkce/databaze.php"; 
if (!maPristup("Z")) {
//	Header("Location: zarukyView.php");
	exit;
}

if (!isset($_POST['akce'])) {
	$_POST["rozbalene"] = $_SESSION["rozbalene"];
	$_POST["zobrazit"] = $_SESSION["zobrazit"];
} else {
	$_SESSION["rozbalene"] = $_POST["rozbalene"];
	$_SESSION["zobrazit"] = $_POST["zobrazit"];
}

$_POST["id_zadal"] = $_SESSION["id_jmeno"];
if (!empty($_POST["id_akce"])) 
	$_POST["zmenil"] = $_SESSION["id_jmeno"];

switch ($_POST["akce"]) {
	case 'Nový pøedmìt':
	case 'Uložit': 
		uloz("akce",$_POST,$_POST["id_akce"]);
		if ($_POST["id_akce"]>0 and $_POST["pocPolozek"]>0) 
			for ($i=1;$i<=$_POST["pocPolozek"];$i++) {
				$Polozka = $_POST["id_polozky_".$i];
				foreach($_POST as $klic => $hodnota) {
					$dbKlic = substr($klic,0,strrpos($klic,'_'));
					if (strrpos($klic,'_')>0 and $dbKlic."_".$i == $klic) 
						$_POST[$dbKlic] = $hodnota;
				}
				$_POST["konec_zaruky"] = GoMonth($_POST["prevzeti"],$_POST["zaruka"]);
				$_POST["prevzeti"] = DateCzEn($_POST["prevzeti"]);
				uloz("akce_polozky",$_POST,$Polozka);
			}
		break;
	case 'Smazat': 
			$data = array ("stav" => "S", "id_akce" => $_POST["id_akce"]);
			uloz("akce",$data,$data["id_akce"]);
			uloz("akce_polozky",$data,$data["id_akce"]); 
	case 'Nová akce': $_POST = array ("akce" => "Nová akce");
}

if (empty($_POST["prevzeti_1"])) 
	$_POST["prevzeti_1"] = date("Y-m-d");

foreach($_POST as $klic => $hodnota)
	if (substr($klic,0,4)=='akce') {
		$Polozka = $_POST["id_polozky_".substr($klic,5)]; 
		$Poradi = substr($klic,5);
	}
if (isset($Polozka) && $_POST["id_akce"]>0) {
	switch ($_POST["akce_".$Poradi]) {
		case 'Smazat': 
				$data = array ("stav" => "S", "id_akce" => $_POST["id_akce"], "id_polozky" => $Polozka); 
				uloz("akce_polozky",$data,$Polozka); 
				if ($_POST["pocPolozek"]==1)
					uloz("akce",$data,$Polozka);  break;
		case 'Ovìøit': 
				$data = array ("stav" => "O", "overil" => $_SESSION["id_jmeno"], "id_akce" => $_POST["id_akce"], "id_polozky" => $Polozka);
				uloz("akce_polozky",$data,$Polozka); break;
		case 'Zrušit ovìø.': 
				$data = array ("stav" => "Z", "overil" => '0', "id_akce" => $_POST["id_akce"], "id_polozky" => $Polozka);
				uloz("akce_polozky",$data,$Polozka); break;
	}
}
$filtr = "1=1";
if (!empty($_POST["hledat"])) {
		$filtr .= " and (cislo_silnice like '%".$_POST["hledat"]."%'";
		$filtr .= " or usek like '%".$_POST["hledat"]."%'";
		$filtr .= " or nazev like '%".$_POST["hledat"]."%'";
		$filtr .= " or predmet like '%".$_POST["hledat"]."%'";
		$filtr .= " or dodavatel like '%".$_POST["hledat"]."%'";
		$filtr .= " or osoba like '%".$_POST["hledat"]."%')";
}

if ($_POST["zobrazit"] == 2)
		$filtr .= " and konec_zaruky < '".date("Y-m-d",time()+86400*31)."' and konec_zaruky >= '".date("Y-m-d",time())."'";
//		$filtr .= " and p.stav<>'O' ";
//if (isset($_POST["koncici"]) or $_POST["akce"]<>'Zobrazit')

$query   = "SELECT * FROM akce WHERE id_akce='".$_POST["id_akce"]."' and stav<>'S' LIMIT 1";
$result = mysql_query($query);
if (mysql_num_rows($result)>0 and $radek = mysql_fetch_assoc($result))
	DbToPOST($radek);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head lang="cs">
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
	<meta http-equiv="Content-Language" content="cs">
	<LINK href="zaruky.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="jquery.js"></script>
	<script language="JavaScript" src="seznam.js"></script>
	<title>Správa silnic Olomouckého kraje - sledování záruèních dob</title>
</head>
<body onLoad="ukaz('<?php echo (isset($_POST['id_akce']) or $_POST["akce"] == 'Nová akce')?"oknoDetail":"oknoSeznam";?>');
<?php echo ($_POST["akce"]=="Nový pøedmìt")?"getObj('predmet_".($_POST["pocPolozek"]+1)."').focus();":"";?>">
<div id=telo>
<h2>Sledování záruèních dob</h2>
<div class=ouska>
	<ul>
		<li class=aktivni id=ousko1>Seznam</li>
		<li id=ousko2>Vybraná akce</li>
	</ul>
</div>
<div id=okraj></div>
<div id=oknoSeznam>
	<form action="zaruky.php" method="post" id=formSeznam>
	<ul>
		<li><input type="radio" name="zobrazit" value="1" <?php echo ($_POST['zobrazit'] > 1)?"":"checked";?>> zobrazit vše</li>
		<li><input type="radio" name="zobrazit" value="2" <?php echo ($_POST['zobrazit'] == 2)?"checked":"";?>> jen konèící</li>
		<li><input type="checkbox" name="rozbalene" <?php echo (isset($_POST['rozbalene']))?"checked":"";?>> zobrazit detaily</li>
	</ul>
	<ul>
		<li></li>
	</ul>
	<ul>
		<li>Hledat:</li>
		<li><input class=text type="text" name="hledat" size="20" value="<?php echo $_POST["hledat"];?>"></li>
	</ul>
	<ul>
		<li><input type="submit" name="akce" value="Zobrazit"></li>
	</ul>
	</form>

	<table class=tabulka cellpadding="0" cellspacing="0">
	<tr class=hlavicka><td>Èíslo silnice</td><td>Úsek (lin.stanièení)</td><td>Název akce</td></tr>
	<?php 
	   $limitOd = 0;
	   $limitDo = 100;
	   $query   = "SELECT h.id_akce,cislo_silnice, usek, nazev, dodavatel, osoba, telefon, id_polozky, predmet, nedodelky, prevzeti, konec_zaruky, p.stav FROM akce h left join akce_polozky p on h.id_akce=p.id_akce WHERE id_zadal = '".$_SESSION["id_jmeno"]."' and ".$filtr." and h.stav<>'S' and p.stav<>'S' LIMIT ".$limitOd.",".$limitDo;
	   $result = mysql_query($query);
	   $pocet = mysql_num_rows($result);
	   $radek = mysql_fetch_assoc($result);
	   if ($radek > 0) {
		   $radku = 1;
		   $polozek = 1;
		   $id_akce = 0;
		   do {
		   		if ($id_akce != $radek['id_akce']) {
			   		if ($id_akce != 0) {
						echo "</table></td></tr>\n";
					   $polozek = 1;
					}
			        echo "<tr id=p".$radek['id_akce']." class=".(($radku%2==0)?"sudy":"lichy")." valign=\"bottom\"><td>".$radek['cislo_silnice']."</td><td>".$radek['usek']."</td><td>".$radek['nazev']."</td></tr>\n";
				    echo "<tr id=d".$radek['id_akce']." class=\"detailPol ".(!isset($_POST['rozbalene'])?"skryty":"")."\"><td colspan=\"3\">";
			  	    echo "<table  cellpadding=\"0\" cellspacing=\"0\"><tr class=polozkaH><td colspan=\"3\">".$radek['dodavatel'].", ".$radek['osoba'].", ".$radek['telefon']."&nbsp;</td></tr>\n";
					$radku += 1;
				}
				if (!is_null($radek['id_polozky'])) {
					$dniDoKonce = ceil((strtotime($radek["konec_zaruky"])-time())/86400);
					echo "<tr class=\"polozkaP".(($radek["stav"]=='O')?" overen":(($dniDoKonce<31)?" konci":""))."\"><td>".$polozek.". ".$radek['predmet']."</td><td>".DateEnCz($radek['prevzeti'])." - ".DateEnCz($radek['konec_zaruky'])."</td><td>".(($radek["stav"]=='O')?" ovìøeno":(($dniDoKonce<=0)?"skonèila!":(($dniDoKonce<31)?"< $dniDoKonce dní":"")))."</td></tr>\n";
					if (!empty($radek['nedodelky']))
						echo "<tr class=\"polozkaP".(($radek["stav"]=='O')?" overen":(($dniDoKonce<31)?" konci":""))."\"><td colspan=\"2\">&nbsp;&nbsp;&nbsp;&nbsp;".$radek['nedodelky']."</td></tr>\n";
					$polozek += 1;
				}
			  	$id_akce = $radek['id_akce'];
		   } while ($radek = mysql_fetch_assoc($result));
			echo "</table></td></tr>\n";
		}
	?>
	</table>
</div>
<div id=oknoDetail>
	<form action="zaruky.php" method="post" enctype="multipart/form-data" id="formAkce">
		<input type="hidden" name="id_akce" value="<?php echo $_POST["id_akce"];?>">
		<div class=vpravo>
			<input class=button type="submit" name="akce" value="Nová akce" accesskey="n"><br><input class=button type="submit" name="akce" value="Smazat">
		</div>	
		<label>Název akce:</label><input class=text type="text" name="nazev" size="87" maxlength="80" value="<?php echo $_POST["nazev"];?>"><br>
		<label>Úsek (lin.stanièení):</label><input class=text type="text" name="usek" size="87" maxlength="90" value="<?php echo $_POST["usek"];?>"><br>
		<label>Èíslo silnice:</label><input class=text type="text" name="cislo_silnice" size="20" maxlength="20" value="<?php echo $_POST["cislo_silnice"];?>"><br>
		<label>Adresa dodavatele:</label><input class=text type="text" name="dodavatel" size="87" maxlength="80" value="<?php echo $_POST["dodavatel"];?>"><br>
		<label>Kontaktní osoba:</label><input class=text type="text" name="osoba" size="55" maxlength="50" value="<?php echo $_POST["osoba"];?>">Telefon: <input class=text type="text" name="telefon" size="18" maxlength="14" value="<?php echo $_POST["telefon"];?>"><br><br>
		<div class="predmet">
			<?php 
			   $limitOd = 0;
			   $limitDo = 30;
			   $query   = "SELECT * FROM akce_polozky WHERE id_akce='".$_POST["id_akce"]."' and stav <> 'S' LIMIT ".$limitOd.",".$limitDo;
			   $result = mysql_query($query);
			   $pocet = mysql_num_rows($result);
			   $i = 1;
			   while ($radek = mysql_fetch_assoc($result) or $i==1 or ($_POST["akce"] == 'Nový pøedmìt' and $i<=$pocet+1)) {
					if ($i<=$pocet) {
	 	 				foreach($radek as $klic => $hodnota)
							$_POST[$klic."_".$i] = $hodnota;
						$dniDoKonce = ceil((strtotime($_POST["konec_zaruky_".$i])-time())/86400);
					}
					echo "<div class=vpravo>\n";
					echo "<input class=button type=\"submit\" name=\"akce_".$i."\" value=\"".(($_POST["stav_".$i]=='O')?"Zrušit ovìø.":"Ovìøit")."\"><br> <input class=button type=\"submit\" name=\"akce_".$i."\" value=\"Smazat\">";
					echo "</div>\n<div class=\"predmet\">\n";

					echo "<label>Pøedmìt záruky:</label><input type=\"hidden\" name=\"id_polozky_".$i."\" value=\"".$_POST["id_polozky_".$i]."\"><input class=text type=\"text\" name=\"predmet_".$i."\" size=\"85\" maxlength=\"80\" value=\"".$_POST["predmet_".$i]."\"><br>\n";
					echo "<label>Datum pøevzetí:</label><input class=\"cislo text\" type=\"text\" name=\"prevzeti_".$i."\" size=\"10\" maxlength=\"10\" value=\"".((empty($_POST["prevzeti_".$i]))?DateEnCz($_POST["prevzeti_".($i-1)]):DateEnCz($_POST["prevzeti_".$i]))."\"> Délka záruky (mìsícù): <input class=\"cislo text\" type=\"text\" name=\"zaruka_".$i."\" size=\"3\" maxlength=\"3\" value=\"".$_POST["zaruka_".$i]."\"> Ukonèení záruky: <span ".(($_POST["stav_".$i]=='O')?" class=overen":(($dniDoKonce<31)?" class=konci":"")).">".DateEnCz($_POST["konec_zaruky_".$i]).(($_POST["stav_".$i]=='O')?" ovìøeno!":(($dniDoKonce<=0)?" skonèila!":(($dniDoKonce<31)?" za $dniDoKonce dní":"")))."</span><br>\n";
					echo "<label>Nedodìlky:</label><input class=\"text\" type=\"text\" name=\"nedodelky_".$i."\" size=\"85\" maxlength=\"80\" value=\"".$_POST["nedodelky_".$i]."\"> \n</div>\n";
				    $i += 1;
			   }
			   $_POST["pocPolozek"] = $i-1;
			?>
		</div>
		<br><label>&nbsp;</label><input type="hidden" name="pocPolozek" value="<?php echo $_POST["pocPolozek"]; ?>"><input class=button type="submit" name="akce" value="Uložit" accesskey="u"> <input class=button type="submit" name="akce" value="Nový pøedmìt" accesskey="p">
	</form>
</div>
</div>
</body>
</html>
