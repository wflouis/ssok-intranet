<?php
include "funkce/funkce.php"; 
if (!maPristup()) 
	exit;
include "funkce/databaze.php"; 
if (isset($_POST["stredisko"]))
	$stredisko = $_POST["stredisko"];
else
	$stredisko = "";

if (!isset($_POST["internet"]))
	$_POST["internet"] = '0';

if (isset($_POST["akce"])) {
   switch ($_POST["akce"]) {
	case "Zmìna støedisek": Header("Location: strediska.php?stredisko=".$_POST["stredisko"]); break;
	case "Seradit": 
		if (empty($_POST["Sloupec"]) || $_SESSION["smer"]==" asc")
			$_SESSION["smer"] = " desc";
		else
			$_SESSION["smer"] = " asc";
		break;
	case "Uložit": 
		if (!empty($_POST["jmeno"]))
			if (uloz("seznam",$_POST,$_POST["id_jmeno"])) {
			   smaz("pristPrava","id_jmeno",$_POST["id_jmeno"],0);
			   $klic=0;
			   $_POST["prava"] = 1;
			   if (isset($_POST["moduly"]))
				   foreach($_POST["moduly"] as $_POST["id_modulu"])
			   		    uloz("pristPrava",$_POST,$klic);  
			}
		break;
	case "Smazat": 
		if (smaz("seznam","id_jmeno",$_POST["id_jmeno"],1))
						smaz("pristPrava","id_jmeno",$_POST["id_jmeno"],0);
	case "Nový":
		$_POST = array (stredisko => $stredisko); break;
   }
}
if (empty($_POST["stredisko"]))
	if (!empty($_GET["stredisko"]))
		$_POST["stredisko"] = $_GET["stredisko"];
	else
		$_POST["stredisko"] = "-";
if (empty($_POST["Sloupec"]))
	$_POST["Sloupec"] = "jmeno";
if (isset($_POST["nacist"]) && $_POST["nacist"]=='1' && !empty($_POST["id_jmeno"])) {
	$result = mysqli_query($_SESSION["link"],"SELECT * FROM seznam WHERE id_jmeno='".$_POST["id_jmeno"]."' LIMIT 1");
	if (mysqli_num_rows($result)>0 and $radek = mysqli_fetch_assoc($result))
		DbToPOST($radek);
	$_POST["moduly"] = array();
	$result = mysqli_query($_SESSION["link"],"SELECT id_modulu FROM pristPrava WHERE id_jmeno='".$_POST["id_jmeno"]."' and prava = '1'");
	$pocet = mysqli_num_rows($result);
	for($i = 0;$i<$pocet;$i++) {
		$radek = mysqli_fetch_row($result);
		$_POST["moduly"][$i]=$radek[0];
	}
	$_POST["najit"] = "";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
<?php
if (maPristup("T",true)) 
	echo "<LINK href=\"seznam.css\" type=text/css rel=stylesheet>"; 
?>
	<script language="JavaScript" src="jquery.js"></script>
	<script language="JavaScript" src="funkce.js"></script>	
</head>
<body onload="document.razeni.najit.focus()">
<span id=telKUOK><a href="#" onClick="window.open('soubor.php?adresar=seznam&soubor=telKUOK.xls');"> <img src="img\xls.gif" border="0"> Telefonní seznam KÚOK</a></span>
<p class=N3>Telefonní seznam</p>
<form name="razeni" action="seznam.php" method="post">
<select name="stredisko" onChange="document.razeni.najit.value='';submit()">
<?php $result=mysqli_query($_SESSION["link"],"SELECT * FROM seznam_str ORDER BY poradi DESC");
	while ($radek = mysqli_fetch_assoc($result)) {
		echo "<option value=\"".$radek["zkratka"]."\">".$radek["nazev"]."</option>\n";
	} 
?>
</select><br>
<input type="hidden" name="nacist" value="0">
<input type="hidden" name="akce" value="">
<input type="hidden" name="Sloupec" value="jmeno">
<input type="hidden" name="id_jmeno" value="<?php echo $_POST["id_jmeno"]; ?>">
<input class="enter" type="text" name="najit" value="<?php echo isset($_POST["najit"])?$_POST["najit"]:""; ?>"> <input type="submit" name="akce" value="Najít"><br><br><br><br>
<?php
if (maPristup("T",true)) 
	include "TelForm.php"; 
?>
</form>
<div style="clear: both"></div><br>
<?php if ($_POST["stredisko"]=='-')
		echo "<div class=chyba>Vyberte nìjaké støedisko nebo cestmistrovství!!!</div><br>";
	else
		echo "<div class=poznamka>Tip: kliknutím na pole <em>jméno</em> nebo <em>støedisko</em> abecednì seøadíte vybraný sloupec.</div><br>";
?>
<script>document.razeni.stredisko.value=<?php  echo "\"".$_POST["stredisko"]."\""; ?></script>
<table class=abs cellpadding="0" cellspacing="0" style="left: 300; top:70">
<tr><td id=r1s1 class=N0></td><td id=r1s2></td></tr>
<tr><td id=r2s1 class=N0></td><td id=r2s2></td></tr>
<tr><td id=r3s1 class=N0></td><td id=r3s2></td></tr>
<tr><td id=r4s1 class=N0></td><td id=r4s2></td></tr>
<tr><td id=r5s1 class=N0></td><td id=r5s2></td></tr>
<tr><td id=r6s1 class=N0></td><td id=r6s2></td></tr>
</table>
<script>
with (document) {
<?php
	$Sql  = "SELECT * FROM strediska ";
	if ($_POST["stredisko"]=="%")
		$Sql .= "WHERE stredisko LIKE 'SSOK'";
	else
		$Sql .= "WHERE stredisko LIKE '".$_POST["stredisko"]."'";
	$adresa=mysqli_query($_SESSION["link"],$Sql);
	$pocet=mysqli_num_rows($adresa);
	for($i=1; $i<=$pocet; $i++) {
		$radek = mysqli_fetch_assoc($adresa);
		echo "r".$i."s1.innerText=\"".$radek["nadpis"]."\";\n";
		if (strpos($radek["text"], "@"))
			echo "r".$i."s2.innerHTML=\"<a href='mailto:".$radek["text"]."'>".$radek["text"]."</a>\";";
		else
			echo "r".$i."s2.innerText=\"".$radek["text"]."\";";
	} 
?>
}
</script>

<table id=seznam cellpadding="3" cellspacing="0">
<thead class=HlTab><td onClick="serad('jmeno')">Jméno 
<?php 
	if ($_POST["Sloupec"]=="jmeno")
		if ($_SESSION["smer"]==" asc")
	 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
	 	else
	 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
    echo "</td><td>funkce</td><td width=\"90\">telefon</td><td width=\"95\" onClick=\"serad('stredisko')\" colspan=\"2\">Støedisko ";
	if ($_POST["Sloupec"]=="stredisko")
		if ($_SESSION["smer"]==" asc")
	 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
	 	else
	 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
	if (maPristup("T",true)) 
		echo "</td><td>Internet</td><td>Oprávnìní</td></thead>";
	else
		echo "</td></thead>";
	$Sql  = "SELECT * FROM seznam ";
	if (!empty($_POST["najit"])) {
		$_POST["stredisko"] = "%";
		$Sql .= "WHERE jmeno LIKE '%".$_POST["najit"]."%' ";
	} else
		$Sql .= "WHERE stredisko LIKE '".$_POST["stredisko"]."'";
	if (!empty($_POST["Sloupec"]))
		$Sql .= "ORDER BY ".$_POST["Sloupec"]."".$_SESSION["smer"];
	$seznam=mysqli_query($_SESSION["link"],$Sql); 
	$pocet=mysqli_num_rows($seznam);
	for($i=1; $i<=$pocet; $i++) {
		$radek = mysqli_fetch_assoc($seznam);
		echo "<TR id=".$radek["id_jmeno"];
		if ($i%2 == 0) 
			echo " class=suda>";
		else
			echo " class=licha>";
		echo "<td>".$radek["jmeno"]."</td><td>".$radek["funkce"]."</td> <td>".$radek["telefon"]."</td><td>".$radek["stredisko"]."</td>";
		$radek["email"] = trim($radek["email"]);
		$radek["mobil"] = trim($radek["mobil"]);
		if (empty($radek["email"]))
			echo "<td>";
		else
			echo "<td><a href=\"mailto:".$radek["email"]."\"><img src=\"img/email.gif\" alt=\"".$radek["email"]."\" border=\"0\"></a>";
		if (empty($radek["mobil"]))
			echo "</td>";
		else
			echo " <img src=\"img/tel.gif\" alt=\"".$radek["mobil"]."\" border=\"0\"></td>";
		if (maPristup("T",true)) {
			echo "<td>".(($radek["internet"])?"A":"")."</td>";
			$zkratky = mysqli_query($_SESSION["link"],"SELECT zkratka, m.id_modulu FROM pristPrava p, moduly m WHERE p.id_modulu = m.id_modulu and id_jmeno='".$radek["id_jmeno"]."' and prava = '1' ORDER BY 2");
			$radku = mysqli_num_rows($zkratky);
			$prava = "";
			for($p = 0;$p<$radku;$p++) {
				$zkratka = mysqli_fetch_row($zkratky);
				$prava .= $zkratka[0];
			}
			echo "<td>$prava</td>";
		}
		echo "</tr>\n";
	} 
?>
</table>

</body>
</html>
