<?php
include "funkce/funkce.php"; 
if (!maPristup("T")) 
	exit; 
include "funkce/databaze.php"; 
	
function ulozTexty() {
    $result = mysqli_query($_SESSION["link"],"DELETE FROM strediska WHERE stredisko = '".$_POST["stredisko"]."'"); 
	for($i=1; $i<=6; $i++) {
		if (!empty($_POST["rn_$i"])) {
		    $result = mysqli_query("INSERT INTO strediska (stredisko , nadpis, text) VALUES ('".$_POST["stredisko"]."','".$_POST["rn_$i"]."','".$_POST["rt_$i"]."')");  
		}		
	}
}

if (empty($_POST["stredisko"]))
	if (empty($_GET["stredisko"]))
		$_POST["stredisko"] = $_POST["zkratka"];
	else
		$_POST["stredisko"] = $_GET["stredisko"];
switch ($_POST["akce"]) {
	case "Zpìt na seznam": Header("Location: eseznam.php?stredisko=".$_POST["stredisko"]); break;
	case "Uložit": 
		if (!empty($_POST["zkratka"])) {
			uloz("seznam_str",$_POST,$_POST["id_str"]);
			ulozTexty();
		}
		break;
	case "Smazat": $result = mysqli_query($_SESSION["link"],"DELETE FROM seznam_str WHERE id_str = '".$_POST["id_str"]."' LIMIT 1");
				   $result = mysqli_query($_SESSION["link"],"DELETE FROM strediska WHERE stredisko = '".$_POST["stredisko"]."'");  
	case "Nové":
		$_POST = array (); 
}
if (!empty($_POST["stredisko"])) {
	$query   = "SELECT * FROM seznam_str WHERE zkratka='".$_POST["stredisko"]."' LIMIT 1";
	$_POST = array (); 
	$result = mysqli_query($_SESSION["link"],$query);
	if (mysqli_num_rows($result)>0 and $radek = mysqli_fetch_assoc($result))
		DbToPOST($radek);
	$_POST["stredisko"] = $_POST["zkratka"];
	$adresa=mysqli_query($_SESSION["link"],"SELECT * FROM strediska WHERE stredisko LIKE '".$_POST["stredisko"]."'");
	$pocet=mysqli_num_rows($adresa);
	for($i=1; $i<=$pocet; $i++) {
		$radek = mysqli_fetch_assoc($adresa);
		$_POST["rn_$i"]=$radek["nadpis"];
		$_POST["rt_$i"]=$radek["text"];
	} 
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
	<LINK href="seznam.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="funkce.js"></script>	
</head>
<body onload="document.razeni.zkratka.focus()">
<p class=N3>Zmìny v seznamu støedisek</p>
<form name="razeni" action="strediska.php" method="post">
<input type="hidden" name="id_str" value="<?php echo $_POST["id_str"]; ?>">
<input type="hidden" name="akce" value="">
<div class=levySl>Støedisko:</div><select name="stredisko" onChange="submit()">
<?php $result=mysqli_query($_SESSION["link"],"SELECT * FROM seznam_str ORDER BY poradi DESC");
	while ($radek = mysqli_fetch_assoc($result)) {
		echo "<option value=\"".$radek["zkratka"]."\">".$radek["nazev"]."</option>\n";
	} 
?>
</select><br>
<script>document.razeni.stredisko.value=<?php  echo "\"".$_POST["stredisko"]."\""; ?></script>
<div class=levySl>Zkratka:</div><input type="text" name="zkratka" size="4" maxlength="4" value="<?php echo $_POST["zkratka"]; ?>"><br>
<div class=levySl>Název:</div><input type="text" name="nazev" size="50" maxlength="50" value="<?php echo $_POST["nazev"]; ?>"><br>
<div class=levySl>Poøadí:</div><input type="text" class=cislo name="poradi" size="3" maxlength="3" value="<?php echo $_POST["poradi"]; ?>"><br><br>
<div class=levySl></div><input type="submit" name="akce" value="Uložit"> <input type="submit" name="akce" value="Nové"> <input type="button" id=akce name="akce" value="Smazat"> <input type="submit" name="akce" value="Zpìt na seznam"><br><br>
1. <input type="text" name="rn_1" size="30" maxlength="30" value="<?php echo $_POST["rn_1"]; ?>"> <input type="text" name="rt_1" size="40" maxlength="40" value="<?php echo $_POST["rt_1"]; ?>"> <br>
2. <input type="text" name="rn_2" size="30" maxlength="30" value="<?php echo $_POST["rn_2"]; ?>"> <input type="text" name="rt_2" size="40" maxlength="40" value="<?php echo $_POST["rt_2"]; ?>"> <br>
3. <input type="text" name="rn_3" size="30" maxlength="30" value="<?php echo $_POST["rn_3"]; ?>"> <input type="text" name="rt_3" size="40" maxlength="40" value="<?php echo $_POST["rt_3"]; ?>"> <br>
4. <input type="text" name="rn_4" size="30" maxlength="30" value="<?php echo $_POST["rn_4"]; ?>"> <input type="text" name="rt_4" size="40" maxlength="40" value="<?php echo $_POST["rt_4"]; ?>"> <br>
5. <input type="text" name="rn_5" size="30" maxlength="30" value="<?php echo $_POST["rn_5"]; ?>"> <input type="text" name="rt_5" size="40" maxlength="40" value="<?php echo $_POST["rt_5"]; ?>"> <br>
6. <input type="text" name="rn_6" size="30" maxlength="30" value="<?php echo $_POST["rn_6"]; ?>"> <input type="text" name="rt_6" size="40" maxlength="40" value="<?php echo $_POST["rt_6"]; ?>"> <br>
</form>
</body>
</html>
