<?php 

session_start();

if (!isset($_SESSION["administrator"])) {

	Header("Location: index.html");

	exit;

}

if (isset($_GET["smer"]))

	$_SESSION["posun"]+=$_GET["smer"];

if (!isset($_POST["zalozka"]))
	$_POST["zalozka"]=1;

include "databaze.php";

if (isset($_POST["user"]))
	$_SESSION["user"] = $_POST["user"];

if (isset($_POST["tlacitko"])) {

	if (isset($_POST["zaokrouhleni"])) {

		$query = "update DochParametry set zaokrouhleni='".max($_POST["zaokrouhleni"],1)."',pridatNaZacatku='".$_POST["pridatNaZacatku"]."',pridatNaKonci='".$_POST["pridatNaKonci"]."',generovatDovolenou='".$_POST["generovatDovolenou"]."' where 1=1";

	}

	if (isset($_POST["sdeleni"])) {

		$query = "update dochSdeleni set sdeleni='".$_POST["sdeleni"]."' where 1=1"; 

	}

	if (isset($_POST["user"])) {

		switch ($_POST["tlacitko"]) {

		case "Ulo�":

			if ($_POST["user"]==0)

				$query = "insert into DochZamestnanci (osCislo,prijmeni,jmeno,uvazek,pracoviste) values ('".$_POST["osCislo"]."','".$_POST["prijmeni"]."','".$_POST["jmeno"]."','".$_POST["uvazek"]."','".$_POST["pracoviste"]."')";

			else

				$query = "update DochZamestnanci set osCislo='".$_POST["osCislo"]."',prijmeni='".$_POST["prijmeni"]."',jmeno='".$_POST["jmeno"]."',uvazek='".$_POST["uvazek"]."',pracoviste='".$_POST["pracoviste"]."',heslo='".$_POST["heslo"]."',ip='' where id='".$_POST["user"]."'";

			break;

		case "Sma�":

				$query = "delete from DochZamestnanci where id='".$_POST["user"]."'"; 

				$_SESSION["user"] = "0";

		}

	}

	if (isset($_POST["zaznam"])) {

		$cas = substr($datumOd,0,8).trim($_POST["den"])." ".$_POST["cas"]; 

		switch ($_POST["tlacitko"]) {

		case "Ulo�":

			if ($_POST["zaznam"]==0) {

				$query = "insert into DochZaznamy (idZam,cas,idTypuZaznamu) values ('".$_SESSION["user"]."','".$cas."','".$_POST["idTypuZaznamu"]."')"; 

				unset($_POST); $_POST["zaznam"]=0;

			} else

				$query = "update DochZaznamy set cas='".$cas."',idTypuZaznamu='".$_POST["idTypuZaznamu"]."' where id='".$_POST["zaznam"]."'";

			break;

		case "Sma�":

				$query = "delete from DochZaznamy where id='".$_POST["zaznam"]."'"; unset($_POST); $_POST["zaznam"]=0;

		}

		$_POST["zalozka"] = 2;

	}

	$result=mysqli_query($spojeni, $query);

}



$query="select * from DochZamestnanci where id='".$_SESSION["user"]."'";
$result=mysqli_query($spojeni, $query);
$zamestnanec=mysqli_fetch_assoc($result); 

?>
<!DOCTYPE HTML">
<html lang="cs-CZ">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">
	<title>Evidence doch�zky</title>
	<link rel="stylesheet" type="text/css" href="styly.css">
	<script type="text/javascript" src="jquery.js"></script>
	<script language="JavaScript" src="funkce.js" type="text/javascript"></script>
	<style>
		#o<?php echo $_POST["zalozka"]; ?> {
			display: block;
		}
	</style>
</head>
<body>
<div class="horniOkraj"><h1>Evidence doch�zky</h1></div>
<div class="zalozky"><a href="#" id="z1" <?php echo ($_POST["zalozka"]==1)?"class=\"aktivni\"":"";?>>Zam�stnanci</a><a href="#" id="z2" <?php echo ($_POST["zalozka"]==2)?"class=\"aktivni\"":"";?>>Z�znamy</a><a href="#" id="z3">Doch�zkov� list</a><a href="#" id="z4" <?php echo ($_POST["zalozka"]==4)?"class=\"aktivni\"":"";?>>Nastaven�</a><a href="#" id="z5" <?php echo ($_POST["zalozka"]==5)?"class=\"aktivni\"":"";?>>Sd�len�</a></div>
<div id="o1" class="telo"><br>
	<h2>Seznam zam�stnanc�</h2>

	<form action="administrace.php" method="post" name="seznam" id="seznam">

		<input type="hidden" name="zalozka" value="1">

		<input type="hidden" name="user" id="user" value="<?php echo $_SESSION["user"]; ?>">

		<span class="levySl">Osobn� ��slo:</span> <input type="text" class="cislo" name="osCislo" id="osCislo" size="4" maxlength="4" value="<?php echo $zamestnanec["osCislo"]; ?>">

			<span class="sloupec">Prijmeni:</span> <input type="text" name="prijmeni" id="prijmeni" size="30" maxlength="30" value="<?php echo $zamestnanec["prijmeni"]; ?>"> 

			Jm�no: <input type="text" name="jmeno" id="jmeno" size="30" maxlength="30" value="<?php echo $zamestnanec["jmeno"]; ?>"><br>

		<span class="levySl">�vazek:</span> <input type="text" class="cislo" name="uvazek" id="uvazek" size="4" maxlength="4" value="<?php echo $zamestnanec["uvazek"]; ?>"> <span class="sloupec">Pracovi�t�:</span> <input type="text" name="pracoviste" id="pracoviste" size="30" maxlength="30" value="<?php echo $zamestnanec["pracoviste"]; ?>">

			Heslo: <input type="text" name="heslo" id="heslo" size="10" maxlength="10" value="<?php echo $zamestnanec["heslo"]; ?>"><br><br>

		<input type="submit" name="tlacitko" value="Ulo�"><input type="button" name="tlacitko" value="Nov�" onClick="novy()"><input type="submit" name="tlacitko" value="Sma�" onclick="return window.confirm('Opravdu chete smazat vybran�ho zam�stnance?');">

	</form>

	<table class="seznamZam" cellspacing="0" cellpadding="0">

	<tr class="zahlavi"><td>Os.��slo</td><td>p��jmen�</td><td>jm�no</td><td>�vazek</td><td>pracovi�t�</td></tr>

<?php 

	$query="select * from DochZamestnanci where 1=1 order by osCislo";

	$result=mysqli_query($spojeni, $query);

	$i = 0;

	while($radek=mysqli_fetch_assoc($result)) {

		echo "<tr id=\"r".$radek["id"]."\"";

		if ($i%2==0)

			echo ">";

		else

			echo " class=\"licha\">";

		echo "<td class=\"zarVpravo\">".$radek["osCislo"]."</td><td>".$radek["prijmeni"]."</td><td>".$radek["jmeno"]."</td><td class=\"zarVpravo\">".$radek["uvazek"]."</td><td>".$radek["pracoviste"]."</td></tr>";

	}

?>

	</table>

</div><br>

<?php 

	include "zaznamy.php";

?>

<div id="o4" class="telo">

	<h2>Nastaven� parametr� programu:</h2>

	<form action="administrace.php" method="post" name="parametry" id="parametry">

		<input type="hidden" name="zalozka" value="4">

		<span class="levySl25">Zaokrouhlen� (min):</span> <input type="text" class="cislo" name="zaokrouhleni" size="2" maxlength="2" value="<?php echo $parametry["zaokrouhleni"];?>"><br> 

		<span class="levySl25">P�idat na za��tku (min):</span> <input type="text" class="cislo" name="pridatNaZacatku" size="2" maxlength="2" value="<?php echo $parametry["pridatNaZacatku"];?>"><br>

		<span class="levySl25">P�idat na konci (min):</span> <input type="text" class="cislo" name="pridatNaKonci" size="2" maxlength="2" value="<?php echo $parametry["pridatNaKonci"];?>"><br>

		<span class="levySl25">P�i odprac. m�n� ne� </span> <input type="text" class="cislo" name="generovatDovolenou" size="2" maxlength="2" value="<?php echo $parametry["generovatDovolenou"];?>">%  fondu prac.doby generovat p�l den dovolen�.<br><br>

		<input type="submit" name="tlacitko" value="Ulo�">

	</form>

	<p class="poznamka">Pro zpracov�n� doch�zkov�ho listu je mo�n� jednotliv� �asov� z�znamy upravit o v��e uveden� korekce. P�i zaokrouhlen� na 15 min. program 

	zaokrouhl� p��chod na cel�ch 15 min. nahoru (nap�. 7:46 nebo 7:56 zaokrouhl� na 8:00) a odchod na 15 min. dol� (tj. z 15:46 nebo 15:56 na 15:45). 

	Korekce "p�idat na za��tku/konci" p�id� u z�znamu p��chod/odchod odpov�daj�c� po�et minut. Nap�. pokud chcete d�t zam�stnanci �asovou rezervu 10 minut p�i p��chodu do zam�stn�n�, 

	zapi�te do pole "p�idat na �a��tku" hodnotu "-10" (nap�. u p��chodu 8:07 se ode�te 10 min na 7:57 a tento �daj se pak p��padn� zaokrouhl�). Podobn� u odchodu uve�te do pole "p�idat na konci" 5 min a z�znam odchodu se o tuto hodnotu prodlou��. 

	Tyto korekce nem�n� p�vodn� z�znamy jednotliv�ch zam�stnanc�, ale pouze jejich prezentaci v doch�zkov�m listu. Proto se nemus�te b�t je zm�nit. 

	</p>

</div>

<div id="o5" class="telo">

	<h2>Sd�len� u�ivatel�m doch�zky:</h2>

	<?php

	$result=mysqli_query($spojeni, "select * from dochSdeleni where 1");

	$sdeleni=mysqli_fetch_assoc($result); 

	?>

	<form action="administrace.php" method="post" name="formSdeleni" id="formSdeleni">

		<input type="hidden" name="zalozka" value="5">

		<textarea cols="60" rows="6" name="sdeleni"><?php echo $sdeleni["sdeleni"];?></textarea><br><br>

		<input type="submit" name="tlacitko" value="Ulo�">

	</form>

	<p class="poznamka">Zde uveden� sd�len� je zobrazeno ka�d�mu u�ivateli doch�zky v prvn� z�lo�ce. Moment�ln� je mo�n� zobrazit pouze jedno sd�len�, kter� je mo�n� aktualizovat. Lze evidovat historii v�ech sd�len�, ale p�edpokl�d�m, �e zat�m to v t�to aplikaci nen� nutn�.

	</p>

</div>

</body>

</html>

