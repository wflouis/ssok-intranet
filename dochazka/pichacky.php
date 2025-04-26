<?php

session_start(); 

if (!isset($_SESSION["user"])) {

	Header("Location: index.html");

	exit;

} else 

	if (isset($_SESSION["administrator"])) {

		Header("Location: administrace.php");

		exit;

	}



if (isset($_GET["smer"]))

	$_SESSION["posun"]+=$_GET["smer"];


if (!isset($_GET["zalozka"]))

	$_GET["zalozka"]=1;


include "databaze.php";

if (isset($_GET["preruseni"])) { 

	if (isset($_SESSION["id"]) && mktime()-$_SESSION["cas"] < 300) {

		$result=mysqli_query($spojeni, "update DochZaznamy set idTypuZaznamu='".$_GET["preruseni"]."',poznamka='".$_GET["poznamka"]."' where id='".$_SESSION["id"]."'");

	} else {

		$result=mysqli_query($spojeni, "insert into DochZaznamy values ('','".$_SESSION["user"]."','".date("Y-m-d H:i:s",mktime())."','".$_GET["preruseni"]."','".$_GET["poznamka"]."')");
		$_SESSION["id"] = mysqli_insert_id($spojeni); 
		$_SESSION["cas"] = mktime();
	}

}

?>

<!DOCTYPE html">

<html lang="cs-CZ">

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">

	<title>Evidence docházky</title>

	<link rel="stylesheet" type="text/css" href="styly.css">

	<script type="text/javascript" src="jquery.js"></script>

	<script language="JavaScript" src="funkce.js" type="text/javascript"></script>

	<style>
		#o<?php echo $_GET["zalozka"]; ?> {
			display: block;
		}
	</style>
</head>

<body>
<div class="horniOkraj"><h1>Evidence docházky</h1></div>
<div class="zalozky"><a href="#" id="z1" <?php echo (($_GET["zalozka"]==1)?"class=\"aktivni\"":"");?>>Píchačky</a>
		     <a href="#" id="z2" <?php echo (($_GET["zalozka"]==2)?"class=\"aktivni\"":"");?>>Záznamy</a>
		     <a href="#" id="z3">Docházkový list</a></div>

<div id="o1" class="telo">

	<br>

	Přihlášen:  

	<?php 

	$query="select * from DochZamestnanci where id='".$_SESSION["user"]."'";

	$result=mysqli_query($spojeni, $query);

	$zamestnanec=mysqli_fetch_assoc($result);

	

	echo $zamestnanec["prijmeni"]." ".$zamestnanec["jmeno"]."<br>";

	echo "<h3>".date("H:i ",$_SESSION["cas"]).$dny[date("w")].date(" - d. ").$mesice[date("m")-1].date(" Y")."</h3>";

	//echo "<h3>".date("H:i - D - d. F Y",$_SESSION["cas"])."</h3>";?>

	<table class="pichacky" align="center">

	<tr>

		<td id="1">Příchod</td>

		<td id="2">Odchod</td>

	</tr>

	<tr>

		<td id="3">Dovolená</td>

		<td id="4">Nemoc</td>

	</tr>

	<tr>

		<td id="5">OČR</td>

		<td id="6">Indispoziční volno</td>

	</tr>

	<tr>

		<td id="7" colspan="2">Omluvená absence</td>

	</tr>

	</table>

	<br>

	<div style="text-align: center">Poznámka: <input type="text" id="poznamka" name="poznamka" size="20" maxlength="20"></div>

	<br><br>

	<div class="sdeleni"><strong>Sdělení: </strong><br>

	<?php

	$result=mysqli_query($spojeni, "select * from dochSdeleni where 1");

	$sdeleni=mysqli_fetch_assoc($result); 

	if (!empty($sdeleni["cas"]))
		echo date("d.m.Y G:i",strtotime($sdeleni["cas"]))." - ".$sdeleni["sdeleni"] ;

	?></div>

	<br>

	<table class="zaznamy" align="center">

	<tr class="den"><td colspan="4">Posledních 5 záznamů:</td></tr>

	<?php

	$query="

	select D.cas, D.poznamka, T.popis from DochZaznamy D left join DochTypyZaznamu T on D.idTypuZaznamu=T.id where D.idZam='".$_SESSION["user"]."' and cas between '$datumOd' and '$datumDo' order by cas desc LIMIT 5";

	$result = mysqli_query($spojeni, $query); 

	while ($radek=mysqli_fetch_assoc($result)) 

			echo "<tr><td>".date("d.m.",strtotime($radek["cas"]))."</td><td class=\"zarVpravo\">".date("G:i",strtotime($radek["cas"]))."</td><td>".$radek["popis"]."</td><td>".$radek["poznamka"]."</td></tr>";

	?>

	</tr>

	</table>

</div>

<?php 

	include "zaznamy.php";

?>

</body>

</html>

