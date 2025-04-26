<?php 
include "funkce/funkce.php"; 
if (!maPristup("S")) 
	exit;
include "funkce/databaze.php"; 

if (empty($_POST["raditPodle"]))
	$_POST["raditPodle"] = "cisloSmlouvy";

if (!isset($_SESSION["smer"]))
	$_SESSION["smer"]=" asc";

if (isset($_POST["akce"])) {
	switch ($_POST["akce"]) {
		case "Seradit": 
			if ($_SESSION["smer"]==" asc")
				$_SESSION["smer"]=" desc";
			else
				$_SESSION["smer"]=" asc";
	}
}
if (!isset($_POST["strana"]))
	$_POST["strana"] = 1;
define("pocStran", 10);
define("pocRadku", 30);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
	<LINK href="registr.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="jquery.js"></script>
	<script language="JavaScript" src="smlouvy.js"></script>
	<?php echo "<style>";
		if (maPristup("P",true)) 
			echo "#oknoSeznam { display: block;}; ";
		echo "</style>";	
	?>
</head>
<body>
<p class=N3>Registr smluv</p>
<div id="oknoSeznam">
	<form action="smlouvyVyber.php" method="post" name="formSeznam" id="formSeznam">
	<input type="hidden" name="raditPodle" value="<?php echo $_POST["raditPodle"];?>">
	<input type="hidden" name="id_smlouvy" value="<?php echo $_POST["id_smlouvy"];?>">
	<input type="hidden" name="partneri" value="0">
	<input type="hidden" name="strana" value="<?php echo $_POST["strana"];?>">
	<span class="levySl">Typ smlouvy:</span> 
	<select name="typSmlouvy" onChange="">
		<option value="%">všechny smlouvy</option>
	<?php $result = mysqli_query($_SESSION["link"],"SELECT * FROM typySmluv WHERE 1=1");
		while ($radek = mysqli_fetch_assoc($result))
			echo "<option value=\"".$radek["id_typuSmlouvy"]."\" ".(($radek["id_typuSmlouvy"]==$_POST["typSmlouvy"])?" selected":"").">".$radek["popis"]."</option>";
	?>
	</select>
	středisko: <select name="stredisko" onChange="">
		<option value="%">všechna střediska</option>
	<?php $result = mysqli_query($_SESSION["link"],"SELECT * FROM seznam_str WHERE 1=1");
		while ($radek = mysqli_fetch_assoc($result))
			echo "<option value=\"".$radek["id_str"]."\"".(($radek["id_str"]==$_POST["stredisko"])?" selected":"").">".$radek["nazev"]."</option>";
	?>
	</select><br>
	<span class="levySl">Rok:</span> 
	<select name="rok" onChange="">
	<?php $rok = date("Y");
		while($rok>='2002') {
			echo "<option value=\"$rok\" ".(($rok==$_POST["rok"])?" selected":"").">$rok</option>";
			$rok--;
		}
	?>
		<option value="%" <?php echo (($_POST["rok"]=="%")?"selected":""); ?>>vše</option>
	</select> &nbsp;&nbsp;Platnost smlouvy od &nbsp;<input type="text" class="cislo" name="platnostOd" size="10" maxlength="10" value="<?php echo $_POST["platnostOd"];?>"> do <input type="text" class="cislo" name="platnostDo" size="10" maxlength="10" value="<?php echo $_POST["platnostDo"];?>"><br>
	<span class="levySl">Hledat výraz:</span> <input class="enter" type="text" name="najit" size="20" maxlength="20" value="<?php echo (isset($_POST["najit"])?$_POST["najit"]:""); ?>"> <input type="submit" name="akce" value="Najít"> <span class="pozn">(hledá v polích smlouva, predmet, ico, nazev partnera)</span>
	</form><br>
	<table cellpadding="0" cellspacing="0"><tr><td>
	<?php 
		if (empty($_SESSION["strana"])) 
			$_SESSION["strana"] = 1;
		if (empty($_POST["strana"])) 
			$_POST["strana"] = $_SESSION["strana"];
		$podminka = "WHERE typSmlouvy LIKE '".$_POST["typSmlouvy"]."'";
		if ($_POST["rok"]!='%')
			$podminka .= " and datumUzavreni between '".$_POST["rok"]."-1-1' and '".$_POST["rok"]."-12-31'";
		if (!empty($_POST["platnostOd"]))
			$podminka .= " and (datumOd = '0000-00-00' and datumUzavreni >= '".DateCzEn($_POST["platnostOd"])."' or datumOd >= '".DateCzEn($_POST["platnostOd"])."')";
		if (!empty($_POST["platnostDo"]))
			$podminka .= " and datumDo <= '".DateCzEn($_POST["platnostDo"])."'";
		if (!empty($_POST["najit"])) {
			$podminka .= " and (cisloSmlouvy LIKE '%".$_POST["najit"]."%' ";
			$podminka .= "or predmet LIKE '%".$_POST["najit"]."%' ";
			$podminka .= "or smlouvy.ico = '".$_POST["najit"]."' ";
			$podminka .= "or smlouvy.rodneCislo LIKE '%".$_POST["najit"]."%' ";
			$podminka .= "or nazev LIKE '%".$_POST["najit"]."%') ";
		} 

		$result = mysqli_query($_SESSION["link"],"SELECT count(smlouvy.id_smlouvy) as pocet FROM (smlouvy left join partneri on (smlouvy.ico=partneri.ico))
				join (select distinct id_smlouvy from smlouvyStr where id_strediska like '".$_POST["stredisko"]."' or  id_strediska in (select id_str from seznam_str where nadrazene='".$_POST["stredisko"]."')) str on str.id_smlouvy=smlouvy.id_smlouvy $podminka"); 
		$zaznam = mysqli_fetch_assoc($result); 
		$posledni = ceil($zaznam["pocet"]/pocRadku);
		switch ($_POST["akce"]) {
			case "zacatek": $_POST["strana"] = 1; break;
			case "vlevo": $_POST["strana"] = ceil($_POST["strana"]/pocStran)*pocStran-pocStran; break;
			case "vpravo": $_POST["strana"] = ceil($_POST["strana"]/pocStran)*pocStran+1; break;
			case "konec": $_POST["strana"] = $posledni; break;
		}
		$_SESSION["strana"] = $_POST["strana"];
		?>
		<table id=posun align="right"><tr><td onClick="navigace()">Nalezeno 
		<?php 
		echo $zaznam["pocet"]." záznamů - ";
		if ($_POST["strana"] > pocStran)
			echo "<img src=\"img\zacatek.gif\"> <img src=\"img\vlevo.gif\">";
		$do = ceil($_POST["strana"]/pocStran)*pocStran;
		$od = $do - pocStran + 1;
		for ($i=$od;$i<=min($do,$posledni);$i++) 
			if ($i == $_POST["strana"])
				echo " <a class=aktivni href=\"#\">$i</a>";
			else
				echo " <a href=\"#\">$i</a>";
		if ($do < $posledni)
			echo " <img src=\"img\vpravo.gif\"> <img src=\"img\konec.gif\">";
	?>
	</td></tr></table><br><br>
	<table id="seznam" cellpadding="3" cellspacing="0">
	<thead class=HlTab><td onClick="serad('cisloSmlouvy')">Smlouva 
	<?php 
		if ($_POST["raditPodle"]=="cisloSmlouvy")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
	    echo "</td><td onClick=\"serad('datumUzavreni')\">uzavřena ";
		if ($_POST["raditPodle"]=="datumUzavreni")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
		echo "</td><td>předmět smlouvy</td><td>cena</td><td>velikost</td></thead>";
		$Sql = "";
		if (isset($_POST["stredisko"])) {
			$Sql  = "SELECT smlouvy.*, partneri.nazev FROM (smlouvy left join partneri on (smlouvy.ico=partneri.ico))
				join (select distinct id_smlouvy from smlouvyStr where id_strediska like '".$_POST["stredisko"]."' or  id_strediska in (select id_str from seznam_str where nadrazene='".$_POST["stredisko"]."')) str on str.id_smlouvy=smlouvy.id_smlouvy ";
			$Sql .= $podminka;
			$Sql .= "ORDER BY ".$_POST["raditPodle"]."".$_SESSION["smer"];
			$Sql .= " LIMIT ".(($_POST["strana"]-1)*pocRadku).",".pocRadku."";
		}
		$seznam=mysqli_query($_SESSION["link"],$Sql); //echo $Sql; 
		while($radek = @mysqli_fetch_assoc($seznam)) {
			echo "<TR id=\"s".$radek["id_smlouvy"]."\"";
			echo " class=\"suda\">";
			echo "<td>".$radek["cisloSmlouvy"]."</td><td>".DateEnCz($radek["datumUzavreni"])."</td> <td>".$radek["predmet"]."</td><td>".$radek["cena"]."</td><td><a href=\"#\" onClick=\"zobrazit('".$radek["id_smlouvy"]."');\">".((!empty($radek["soubor"]))?"<img src=\"img/".strtolower(substr(strchr($radek["soubor"],"."),1,3)).".gif\" border=\"0\"> ".$radek["velikost"]." kB":"")."</a></td>";
			echo "</tr>\n";
			echo "<tr class=\"doplnek\"><td></td><td colspan=\"4\">Sml.strana: ".$radek["nazev"]." Platnost: ".$radek["datumTxt"].(($radek["datumOd"]>"1990-01-01")?" od ".DateEnCz($radek["datumOd"]):"").(($radek["datumDo"]>"1990-01-01")?" do ".DateEnCz($radek["datumDo"]):"")."</td></tr>\n";
		} 
	?>
	</table>
</div>
</body>
</html>
