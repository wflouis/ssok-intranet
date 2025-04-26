<?php
include "funkce/funkce.php"; 
include "funkce/databaze.php"; 
if (!isset($_COOKIE["id_jmeno"])) {
	Header("Location: zamitnuti.html");
	exit;
} 

if (!isset($_POST['stredisko'])) {
	$result = mysql_query("SELECT stredisko FROM seznam WHERE id_jmeno = '".$_COOKIE["id_jmeno"]."'");
    $radek = mysql_fetch_assoc($result);
	$_POST['stredisko'] = $radek["stredisko"];
	$_POST["koncici"] = true;
	$_POST["overene"] = true;
}

$filtr = "1=1";
if (!empty($_POST["hledat"])) {
		$filtr .= " and (cislo_silnice like '%".$_POST["hledat"]."%'";
		$filtr .= " or usek like '%".$_POST["hledat"]."%'";
		$filtr .= " or h.nazev like '%".$_POST["hledat"]."%'";
		$filtr .= " or predmet like '%".$_POST["hledat"]."%'";
		$filtr .= " or dodavatel like '%".$_POST["hledat"]."%'";
		$filtr .= " or osoba like '%".$_POST["hledat"]."%'";
		$filtr .= " or z.jmeno like '%".$_POST["hledat"]."%')";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head lang="cs">
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
	<meta http-equiv="Content-Language" content="cs">
	<LINK href="zaruky.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="seznam.js"></script>
	<title>Správa silnic Olomouckého kraje - sledování záruèních dob</title>
</head>
<body>
<div id=telo>
<h2>Sledování záruèních dob</h2>
<div class=ouska>
	<ul>
		<li class=aktivni id=ousko1>Seznam</li>
	</ul>
</div>
<div id=okraj></div>
<div id=oknoSeznam>
	<form action="zarukyView.php" method="post" id="formSeznam">
	<ul>
		<li><input type="checkbox" name="koncici" <?php echo (isset($_POST['koncici']))?"checked":"";?>> jen konèící</li>
		<li><input type="checkbox" name="overene" <?php echo (isset($_POST['overene']))?"checked":"";?>> jen neovìøené</li>
		<li><input type="checkbox" name="rozbalene" <?php echo (isset($_POST['rozbalene']))?"checked":"";?>> zobrazit detaily</li>
	</ul>
	<ul>
		<li></li>
	</ul>
	<ul>
		<li></li>
		<li><select id="stredisko" name="stredisko">
		<?php $result=mysql_query("SELECT * FROM seznam_str ORDER BY poradi DESC");
			while ($radek = mysql_fetch_assoc($result)) {
				echo "<option value=\"".$radek["zkratka"]."\">".$radek["nazev"]."</option>\n";
			} 
		?>
		</select></li>
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
	<script>getObj("stredisko").value=<?php  echo "\"".$_POST["stredisko"]."\""; ?>;</script>
	<table class=tabulka cellpadding="0" cellspacing="0">
	<tr class=hlavicka><td width="90">Konec záruky</td><td>Pøedmìt záruky</td><td>Stav</td><td>Oblast</td></tr>
	<?php 
	   $limitOd = 0;
	   $limitDo = 100;
	   $query   = "SELECT h.id_akce, cislo_silnice, usek, h.nazev, dodavatel, osoba, h.telefon, id_polozky, predmet, prevzeti, konec_zaruky, p.stav, z.jmeno AS zadal, z.telefon AS zadal_tel, z.email AS zadal_mail, zkratka, seznam_str.nazev AS stredisko, o.jmeno AS overil, o.telefon AS overil_tel, o.email AS overil_mail FROM akce h LEFT JOIN akce_polozky p ON h.id_akce = p.id_akce LEFT JOIN seznam z ON h.id_zadal = z.id_jmeno LEFT JOIN seznam o ON p.overil = o.id_jmeno JOIN seznam_str ON z.stredisko = seznam_str.zkratka WHERE ".$filtr." and h.stav<>'S' and p.stav<>'S' ".(isset($_POST["overene"])?" and p.stav<>'O'":"").(isset($_POST["koncici"])?" and konec_zaruky < '".date("Y-m-d",time()+86400*31)."' and konec_zaruky >= '".date("Y-m-d",time())."'":"")." and z.stredisko like '".$_POST["stredisko"]."' ORDER BY konec_zaruky LIMIT ".$limitOd.",".$limitDo;
	   $result = mysql_query($query);
	   $pocet = mysql_num_rows($result);
	   $radku = 0; 
	   while($radek = mysql_fetch_assoc($result)) {
	   	    $radku += 1;
			$dniDoKonce = ceil((strtotime($radek["konec_zaruky"])-time())/86400);
	        echo "<tr id=p".$radku." class=\"".(($radku%2==0)?"sudy":"lichy").(($radek["stav"]=='O')?" overen":(($dniDoKonce<31)?" konci":""))."\" valign=\"bottom\"><td>".DateEnCz($radek['konec_zaruky'])."</td><td>".$radek['predmet']."</td><td>".(($radek["stav"]=='O')?" ovìøeno":(($dniDoKonce<=0)?"skonèila!":(($dniDoKonce<31)?"< $dniDoKonce dní":"")))."</td><td>".$radek['zkratka']."</td></tr>\n";
		    echo "<tr id=d".$radku." class=\"detailPol ".(!isset($_POST['rozbalene'])?"skryty":"")."\"><td colspan=\"4\">";
	  	    echo "<table  cellpadding=\"0\" cellspacing=\"0\">";
			echo "<tr class=\"polozkaP\"><td class=\"popis\" width=\"90\">Název:</td><td>".$radek['nazev']."</td></tr>\n";
			echo "<tr class=\"polozkaP\"><td class=\"popis\">Úsek:</td><td>".$radek['usek']."</td></tr>\n";
			echo "<tr class=\"polozkaP\"><td class=\"popis\">Silnice:</td><td>".$radek['cislo_silnice']."</td></tr>\n";
			echo "<tr class=\"polozkaP\"><td class=\"popis\">Dodavatel:</td><td>".$radek['dodavatel']."</td></tr>\n";
			echo "<tr class=\"polozkaP\"><td class=\"popis\">Kontakt:</td><td>".$radek['osoba']." Tel: ".$radek['telefon']."</td></tr>\n";
			echo "<tr class=\"polozkaP\"><td class=\"popis\">Zadal:</td><td>".$radek['zadal']." Tel: ".$radek['zadal_tel']." E-mail: ".$radek['zadal_mail']."</td></tr>\n";
			if (!empty($radek["overil"]))
				echo "<tr class=\"polozkaP\"><td class=\"popis\">Overil:</td><td>".$radek['overil']." Tel: ".$radek['overil_tel']." E-mail: ".$radek['overil_mail']."</td></tr>\n";
			echo "</table></td></tr>\n";
	   }
	?>
	</table>
</div>
</div>
</body>
</html>
