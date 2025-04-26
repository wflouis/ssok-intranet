<?php 
include "funkce/funkce.php"; 
if (!maPristup("S")) 
	exit;
include "funkce/databaze.php"; 

if (empty($_POST["raditPodle"]))
	$_POST["raditPodle"] = "ico";
if (isset($_POST["id_smlouvy"]))
	$_POST["id_partnera"]= $_POST["id_smlouvy"];
if (!isset($_SESSION["smer"]))
	$_SESSION["smer"]=" asc";
$akce = "";
if (isset($_POST["akce"])) {
	switch ($_POST["akce"]) {
		case "Uložit": 
			if (!empty($_POST["ico"])) {
				uloz("partneri",$_POST,$_POST["id_partnera"]); 
			}
			break;
		case "Smazat": smaz("partneri","id_partnera",$_POST["id_partnera"],1); 
		case "Seradit": 
			if ($_SESSION["smer"]==" asc")
				$_SESSION["smer"]=" desc";
			else
				$_SESSION["smer"]=" asc";
		case "Najít":
		case "Nový partner":
			$akce = $_POST["akce"];
			$_POST = array (najit => $_POST["najit"],raditPodle => $_POST["raditPodle"],zmeny => $_POST["zmeny"]); break;
		 
	}
}
if (!empty($_POST["id_partnera"])) {
	$result = mysqli_query($_SESSION["link"],"SELECT * FROM partneri WHERE id_partnera='".$_POST["id_partnera"]."' LIMIT 1");
	if (mysqli_num_rows($result)>0 and $radek = mysqli_fetch_assoc($result))
		DbToPOST($radek);
}
$detail = (!empty($_POST["id_partnera"]) or $akce=="Nový partner");
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
		if ($detail) {
			echo "	#oknoSeznam {
				display: none;
			}
			#oknoDetail {
				display: block;
			}";
		} else {
			echo "	#oknoSeznam {
				display: block;
			}
			#oknoDetail {
				display: none;
			}";
		}
		$pravoZmen = maPristup("P",true);
		echo "</style>";	
	?>
</head>
<body onload="window.document.formSeznam.najit.focus()">
<p class=N3>Adresáø smluvních partnerù</p>
<?php
if (maPristup("P",true)) 
	include "partneriDetail.php"; 
?>
<div id="oknoSeznam">
	<form action="partneri.php" method="post" name="formSeznam" id="formSeznam">
	<input type="hidden" name="raditPodle" value="<?php echo $_POST["raditPodle"];?>">
	<input type="hidden" name="id_partnera" value="<?php echo $_POST["id_partnera"];?>">
	<input type="hidden" name="id_smlouvy">
	<input type="hidden" name="okno">
	<input type="hidden" name="partneri" value="1">
	<input type="hidden" name="zmeny" value="1">
	<span class="levySl">Hledat výraz:</span> <input class="enter" type="text" name="najit" size="20" maxlength="20" value="<?php echo (isset($_POST["najit"])?$_POST["najit"]:""); ?>" onChange="this.form.submit();"> <input type="submit" name="akce" value="Najít"> <span class="pozn">(hledá v polích ico, nazev partnera, mìsto)</span>
	</form><br>
	<table id="seznamPartneru" cellpadding="3" cellspacing="0">
	<thead class="HlTab"><td onClick="serad('ico')">IÈ 
	<?php 
		if ($_POST["raditPodle"]=="ico")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
	    echo "</td><td onClick=\"serad('nazev')\">název ";
		if ($_POST["raditPodle"]=="nazev")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
		echo "</td><td>adresa</td></thead>";
		$Sql  = "SELECT * FROM partneri ";
		$Sql .= "WHERE 1=1 ";
		if (!empty($_POST["najit"])) {
			$Sql .= "and (ico LIKE '%".$_POST["najit"]."%' ";
			$Sql .= "or nazev LIKE '%".$_POST["najit"]."%' ";
			$Sql .= "or mesto LIKE '%".$_POST["najit"]."%') ";
		} 
		$Sql .= "ORDER BY ".$_POST["raditPodle"]."".$_SESSION["smer"];
		if (isset($_POST["najit"])) {
			$seznam=mysqli_query($_SESSION["link"],$Sql); //echo $Sql;
			while($radek = mysqli_fetch_assoc($seznam)) {
				echo "<TR ".(($pravoZmen)?"id=\"s".$radek["id_partnera"]."\"":"");
				echo " class=\"suda\">";
				echo "<td>".$radek["ico"]."</td><td>".$radek["nazev"]."</td><td>".$radek["ulice"].", ".$radek["psc"].", ".$radek["mesto"]."</td>";
				echo "</tr>\n";
				echo "<tr class=\"doplnek\"><td></td><td colspan=\"2\">Kontakt: ".$radek["osoba"].", ".$radek["telefon"].", <a href=\"mailto:".$radek["email"]."\">".$radek["email"]."</a> ".$radek["kadresa"]."</td></tr>\n";
			} 
		}
	?>
	</table>
</div>
</body>
</html>
