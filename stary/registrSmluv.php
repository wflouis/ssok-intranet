<?php 
include "funkce/funkce.php"; 
maPristup('X',true);
//if (!maPristup()) 
//	exit;
include "funkce/databaze.php"; 

$_POST["datumUzavreni"] = DateCzEn($_POST["datumUzavreni"]);
$_POST["datumOd"] = DateCzEn($_POST["datumOd"]);
$_POST["datumDo"] = DateCzEn($_POST["datumDo"]);
$_POST["kdy"] = DateCzEn($_POST["kdy"]);
$_POST["uhrazeno"] = DateCzEn($_POST["uhrazeno"]);
switch ($_POST["akce"]) {
	case "Uložit": 
		if (!empty($_POST["cisloSmlouvy"])) {
			if (is_uploaded_file($_FILES['soubor']['tmp_name'])) {
				$_POST["soubor"] = $_POST["cisloSmlouvy"].strchr($_FILES['soubor']['name'],".");
   				move_uploaded_file($_FILES['soubor']['tmp_name'], "/share/Smlouvy/".$_POST["soubor"]);
			}
			uloz("smlouvy",$_POST,$_POST["id_smlouvy"]); 
		}
		break;
	case "Smazat": smaz("smlouvy","id_smlouvy",$_POST["id_smlouvy"],1); unlink("/share/Smlouvy/".$_SESSION["soubor"]);
	case "Nová smlouva":
		$_POST = array (stredisko => $_POST["stredisko"]); unset($_SESSION["soubor"]); break;
}

if (!empty($_POST["id_smlouvy"])) {
	$result = mysql_query("SELECT * FROM smlouvy WHERE id_smlouvy='".$_POST["id_smlouvy"]."' LIMIT 1");
	if (mysql_num_rows($result)>0 and $radek = mysql_fetch_assoc($result))
		DbToPOST($radek);
	$_POST["datumUzavreni"] = DateEnCz($_POST["datumUzavreni"]);
	$_POST["datumOd"] = DateEnCz($_POST["datumOd"]);
	$_POST["datumDo"] = DateEnCz($_POST["datumDo"]);
	$_POST["kdy"] = DateEnCz($_POST["kdy"]);
	$_POST["uhrazeno"] = DateEnCz($_POST["uhrazeno"]);
	$_SESSION["soubor"] = $_POST["soubor"];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
	<LINK href="registr.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="funkce.js"></script>	
</head>
<body>
<p class=N3>Registr smluv</p>
<form action="registrSmluv.php" method="post" enctype="multipart/form-data" name="razeni" id="razeni">

<input type="hidden" name="nacist" value="0">
<input type="hidden" name="id_smlouvy" value="<?php echo $_POST["id_smlouvy"];?>">

<span class="levySl">Typ smlouvy:</span> 
<select name="typSmlouvy" onChange="">
<?php $result = mysql_query("SELECT * FROM typySmluv WHERE 1=1");
	while ($radek = mysql_fetch_assoc($result))
		echo "<option value=\"".$radek["id_typuSmlouvy"]."\" ".(($radek["id_typuSmlouvy"]==$_POST["typSmlouvy"])?" selected":"").">".$radek["popis"]."</option>";
?>
</select>
støedisko: <select name="stredisko" onChange="">
<?php $result = mysql_query("SELECT * FROM seznam_str WHERE hlavni = '1'");
	while ($radek = mysql_fetch_assoc($result))
		echo "<option value=\"".$radek["id_str"]."\"".(($radek["id_str"]==$_POST["stredisko"])?" selected":"").">".$radek["nazev"]."</option>";
?>
</select><br>
<span class="levySl">Èíslo smlouvy:</span> <input type="text" name="cisloSmlouvy" size="20" maxlength="20" value="<?php echo $_POST["cisloSmlouvy"];?>"> Naèíst z: <input type="file" name="soubor" size="30" value="<?php echo $_POST["soubor"];?>"> <br>
<span class="levySl">Datum uzavøení:</span> <input class="datum" type="text" name="datumUzavreni" size="10" maxlength="10" value="<?php echo $_POST["datumUzavreni"];?>"> cena: <input type="text" name="cena" size="30" maxlength="30" value="<?php echo $_POST["cena"];?>"> <br>
<span class="levySl">Smluvní strana (IÈO):</span> <input class="cislo" type="text" name="ico" size="10" maxlength="8" value="<?php echo $_POST["ico"];?>"> <br>
<span class="levySl">Pøedmìt smlouvy:</span> <textarea cols="60" rows="4" name="predmet"><?php echo $_POST["predmet"];?></textarea><br>
<span class="levySl">Doba trvání:</span> <input type="text" name="datumTxt" size="30" maxlength="30" value="<?php echo $_POST["datumTxt"];?>"> od: <input type="text" class="datum" name="datumOd" size="10" maxlength="10" value="<?php echo $_POST["datumOd"];?>"> do: <input type="text" class="datum" name="datumDo" size="10" maxlength="10" value="<?php echo $_POST["datumDo"];?>"><br>
<span class="levySl">Pøipomenout:</span> <input type="checkbox" name="upozornit" value="0"> kdy: <input type="text" name="kdy" size="10" maxlength="10" value="<?php echo $_POST["kdy"];?>"> text: <input type="text" name="text" size="50" maxlength="50" value="<?php echo $_POST["text"];?>"><br>
<span class="levySl">Uhrazeno dne:</span> <input class="datum" type="text" name="uhrazeno" size="10" maxlength="10" value="<?php echo $_POST["uhrazeno"];?>"><br><br>
<span class="levySl">&nbsp;</span> <input type="submit" name="akce" value="Uložit"><input type="submit" name="akce" value="Nová smlouva"><input type="submit" name="akce" value="Smazat">
</form>
</body>
</html>
