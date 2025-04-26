<?php 
include "funkce/funkce.php"; 
if (!maPristup("M")) 
	exit;
include "funkce/databaze.php"; 

if (empty($_POST["raditPodle"]))
	$_POST["raditPodle"] = "invCislo";
if (isset($_POST["id_smlouvy"]))
	$_POST["id_majetku"]= $_POST["id_smlouvy"];
if (!isset($_POST["typMajetku"])) {
	$_POST["invCislo"]= "";
	$_POST["typMajetku"]= 1;
}
if ($_POST["stredisko"]=='17')
	$_POST["stredisko"] = '%';

foreach($_POST as $klic=>$pole) 
	if (ereg("(p[0-9])",$klic))
		$_POST[substr($klic,1,2)]=$pole;
$akce = "";
if (isset($_POST["akce"])) {
	switch ($_POST["akce"]) {
		case "Uložit": 
			if (!empty($_POST["invCislo"])) {
				if (uloz("majetek",$_POST,$_POST["id_majetku"])) {
					smaz("parametryMajetku","majetek",$_POST["id_majetku"]);
					foreach($_POST as $klic=>$pole) 
						if (ereg("(p[0-9])",$klic) && !empty($pole)) {
							$Data = array('typMajetku' => $_POST["typMajetku"], 'majetek' => $_POST["id_majetku"], 'parametr' => substr($klic,1,2), 'hodnota' => $pole);
							$PomKlic=0;
							uloz("parametryMajetku",$Data,$PomKlic);
						}
				}
			}
			break;
		case "Najít": $_POST["id_majetku"] = ""; break;
		case "Seradit": 
			if ($_SESSION["smer"]==" asc")
				$_SESSION["smer"]=" desc";
			else
				$_SESSION["smer"]=" asc";
				break;
		case "Smazat": smaz("majetek","id_majetku",$_POST["id_majetku"],1); 
					   smaz("parametryMajetku","majetek",$_POST["id_majetku"]); 
		case "Nový majetek":
			$akce = $_POST["akce"];
			$_POST = array ('stredisko' => $_POST["stredisko"],'typMajetku' => $_POST["typMajetku"], 'najit' => $_POST["najit"],'raditPodle' => $_POST["raditPodle"],'zmeny' => $_POST["zmeny"]); break;
	}	 
}
if (!empty($_POST["id_majetku"])) {
	$result = mysql_query("SELECT * FROM majetek WHERE id_majetku='".$_POST["id_majetku"]."' LIMIT 1");
	if (mysql_num_rows($result)>0 and $radek = mysql_fetch_assoc($result))
		DbToPOST($radek);
}
$detail = (!empty($_POST["id_majetku"]) or $akce=="Nový majetek");
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
		} else	{
			echo "	#oknoSeznam {
				display: block;
			}
			#oknoDetail {
				display: none;
			}";
		}
		$pravoZmen = maPristup("M",true);
		echo "</style>";	
	?>
</head>
<body>
<p class=N3>Seznam inventárního majetku</p>
<?php
if (maPristup("M",true)) 
	include "majetekDetail.php"; 
?>
<div id="oknoSeznam">
	<form action="majetek.php" method="post" name="formSeznam" id="formSeznam">
	<input type="hidden" name="raditPodle" value="<?php echo $_POST["raditPodle"];?>">
	<input type="hidden" name="id_majetku" value="<?php echo $_POST["id_majetku"];?>">
	<input type="hidden" name="id_smlouvy">
	<input type="hidden" name="partneri" value="1">
	<input type="hidden" name="zmeny" value="1">
	<input type="hidden" name="okno">
	<span class="levySl">Typ majetku:</span> 
	<select name="typMajetku">
	<?php $result = mysql_query("SELECT * FROM typyMajetku WHERE 1=1");
		while ($radek = mysql_fetch_assoc($result))
			echo "<option value=\"".$radek["id_typuMajetku"]."\" ".(($radek["id_typuMajetku"]==$_POST["typMajetku"])?" selected":"").">".$radek["popis"]."</option>";
	?>
	</select>
	støedisko: <select name="stredisko" onChange="">
	<?php $result = mysql_query("SELECT * FROM seznam_str WHERE 1=1 ORDER BY poradi");
		while ($radek = mysql_fetch_assoc($result))
			echo "<option value=\"".$radek["id_str"]."\"".(($radek["id_str"]==$_POST["stredisko"])?" selected":"").">".$radek["nazev"]."</option>";
	?>
	</select><br>
	
	<?php  $Sql = (isset($_POST["typMajetku"]))?"SELECT DISTINCT d.*,p.hodnota FROM definiceParametru d left join parametryMajetku p on (d.id_parametru=p.parametr and d.typMajetku=p.typMajetku) WHERE d.typMajetku='".$_POST["typMajetku"]."' and filtr='1' ORDER BY id_parametru":"";
		$result = @mysql_query($Sql); 
		$parametr = "";
		while ($radek = @mysql_fetch_assoc($result)) {
			if ($parametr <> $radek["id_parametru"]) {
				$parametr = $radek["id_parametru"];
				if ($parametr <> "") 
					echo "</select><br>";		
				echo "<span class=\"levySl\">".$radek["popis"]."</span>";
				echo "<select name=\"p".$radek["id_parametru"]."\">";
	  			echo "<option value=\"%\"></option>";
			}
			echo "<option value=\"".$radek["hodnota"]."\">".$radek["hodnota"]."</option>";
		}
		if ($parametr <> "") 
			echo "</select><br>";		
		?><br>
	<span class="levySl">Hledat výraz:</span> <input class="enter" type="text" name="najit" size="20" maxlength="20" value="<?php echo (isset($_POST["najit"])?$_POST["najit"]:""); ?>""> <input type="submit" name="akce" value="Najít"> <span class="pozn"></span>
	</form><br>
	<table id="seznam" cellpadding="3" cellspacing="0">
	<thead class=HlTab><td onClick="serad('invCislo')">Inv.Èíslo 
	<?php 
		if ($_POST["raditPodle"]=="invCislo")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
	    echo "</td>";
		$PocetParametru = 0;
		$result = mysql_query("SELECT * FROM definiceParametru d WHERE d.typMajetku='".$_POST["typMajetku"]."'");
			while ($radek = mysql_fetch_assoc($result)) {
				echo "<td>".$radek["popis"]."</td>";	
				$PocetParametru++;
			}
		echo "</thead>\n";
		
		$Sql  = "SELECT m.*, parametr, hodnota FROM majetek m left join parametryMajetku p on (id_majetku=majetek) ";
		$Sql .= "WHERE m.typMajetku= '".$_POST["typMajetku"]."' and stredisko like '".$_POST["stredisko"]."' ";
		$najit = "";
		if (!empty($_POST["najit"])) {
			$Sql .= "and (invCislo LIKE '%".$_POST["najit"]."%' or ";
			$najit = "and hodnota LIKE '%".$_POST["najit"]."%'";
		} 
		$podminka = "";
		foreach($_POST as $klic=>$pole) 
			if (ereg("(p[0-9])",$klic) && $pole<>"%") 
				$podminka .= "hodnota LIKE '".htmlspecialchars($pole)."%' or "; 
		if (!empty($podminka))
			if (!empty($_POST["najit"]))
				$Sql .= " majetek IN (SELECT DISTINCT majetek FROM parametryMajetku WHERE ($podminka false) $najit))";
			else
				$Sql .= " and majetek IN (SELECT DISTINCT majetek FROM parametryMajetku WHERE ($podminka false))";
		$Sql .= " ORDER BY ".$_POST["raditPodle"]."".$_SESSION["smer"].", parametr"; //echo $Sql;
		$seznam=mysql_query($Sql); //echo $Sql; 
		$id = 0;
		$i = 0;
		while($radek = mysql_fetch_assoc($seznam)) {
			if ($radek["id_majetku"]<>$id) {
				$id = $radek["id_majetku"];
				$sloupec = 1;
				if ($id <> 0) {
					echo "</tr>\n";
					$i++;
				}
				echo "<TR ".(($pravoZmen)?"id=\"s".$radek["id_majetku"]."\"":"");
				if ($i%2 == 0) 
					echo " class=\"suda\">";
				else
					echo " class=\"licha\">";
				echo "<td>".$radek["invCislo"]."</td>";
			}
			for($index=$sloupec;$index<$radek["parametr"];$index++) {
				echo "<td></td>";
				$sloupec++;
			}
			echo "<td>".$radek["hodnota"]."</td>";
			$sloupec++;
		}
		if ($id <> 0) 
			echo "</tr>\n";
	?>
	</table>
</div>
</body>
</html>
