<?php 
include "funkce/funkce.php"; 
include "funkce/databaze.php"; 
define("cesta","/share/smernice/");

if (isset($_POST["archiv"]))
	$_POST["archiv"] = 1;
if (empty($_SESSION["okno"]))
	$_SESSION["okno"] = "1";
if (!empty($_POST["okno"]))
	$_SESSION["okno"] = $_POST["okno"];

$mimetypes = array(
    'doc'        => 'application/msword',
    'pdf'        => 'application/pdf',
    'xls'        => 'application/vnd.ms-excel',
    'ppt'        => 'application/vnd.ms-powerpoint',
    'xhtml'        => 'application/xhtml+xml',
    'zip'        => 'application/zip',
    'mid'        => 'audio/midi',
    'midi'        => 'audio/midi',
    'mp3'        => 'audio/mpeg',
    'wav'        => 'audio/x-wav',
    'bmp'        => 'image/bmp',
    'gif'        => 'image/gif',
    'jpeg'        => 'image/jpeg',
    'jpg'        => 'image/jpeg',
    'png'        => 'image/png',
    'tif'        => 'image/tiff',
    'html'        => 'text/html',
    'htm'        => 'text/html',
    'txt'        => 'text/plain',
    'rtf'        => 'text/rtf',
    'mpeg'        => 'video/mpeg',
    'mpg'        => 'video/mpeg',
    'mov'        => 'video/quicktime',
    'avi'        => 'video/x-msvideo',
);

if (isset($_POST["plat_od"])) {
	$_POST["plat_od"] = DateCzEn($_POST["plat_od"]);
}
$akce = "";
if (isset($_POST["akce"])) {
	switch ($_POST["akce"]) {
		case "Uložit": 
			if (!empty($_POST["cislo"])) {
				if (is_uploaded_file($_FILES['soubor']['tmp_name'])) {
					$_POST["soubor"] = $_POST["id_smernice"]."_".$_POST["cislo"].strchr($_FILES['soubor']['name'],".");
					$_POST["velikost"] = $_FILES['soubor']['size']/1024;
	   				move_uploaded_file($_FILES['soubor']['tmp_name'], cesta.$_POST["soubor"]);
				}
				uloz("smernice",$_POST,$_POST["id_smernice"]); 
			}
			break;
		case "Smazat": smaz("smernice","id_smernice",$_POST["id_smernice"],1); unlink(cesta.$_SESSION["soubor"]); $_SESSION["okno"] = "1";
		case "Najít":
		case "Nová smìrnice":
			$akce = $_POST["akce"];
			$_POST = array ('najit' => $_POST["najit"],'platnost' => $_POST["platnost"],'archivView' => $_POST["archivView"]); unset($_SESSION["soubor"]); break;
		case "Zobrazit": 
			$result = mysql_query("SELECT soubor FROM smernice WHERE id_smernice='".$_POST["id_smernice"]."' LIMIT 1");
			if (mysql_num_rows($result)>0 and $radek = mysql_fetch_assoc($result)) {
				if (is_file(cesta.$radek["soubor"])) {
					$Pripona = substr(strchr($radek["soubor"],"."),1,3); 
			     	header ("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		    		header('Content-Description: File Transfer');
					header('Content-type: '.$mimetypes[$Pripona]);
					header("Content-Length: ".filesize(cesta.$radek["soubor"]));
				    header('Content-Disposition: attachment; filename='.$radek["soubor"]);
					readfile(cesta.$radek["soubor"]);
					exit;
				}
			} 
			$_POST["id_smernice"] = "";
	}
}
if (!empty($_POST["id_smernice"])) {
	$result = mysql_query("SELECT * FROM smernice WHERE id_smernice='".$_POST["id_smernice"]."' LIMIT 1");
	if (mysql_num_rows($result)>0 and $radek = mysql_fetch_assoc($result))
		DbToPOST($radek);
	$_POST["plat_od"] = DateEnCz($_POST["plat_od"]);
	$_SESSION["soubor"] = $_POST["soubor"];
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
	<LINK href="registr.css" type=text/css rel=stylesheet>
	<script language="JavaScript" src="smlouvy.js"></script>
	<?php echo "<style>";
		switch ($_SESSION["okno"]) {
			case "1": echo "	#oknoSeznam { display: block;}"; break;
			case "2": echo "	#oknoDetail { display: block;}"; break;
		}
		echo "</style>";	
	?>
</head>
<body>
<p class=N3>Registr smìrnic</p>
<?php
$zmeny = false;
if (maPristup("C",true)) {
	include "smerniceDetail.php";
	$zmeny = true;
} 
?>
<div id="oknoSeznam">
	<form action="smerniceNew.php" method="post" name="formSeznam" id="formSeznam">
	<input type="hidden" name="id_smernice" value="<?php echo $_POST["id_smernice"];?>">
	<input type="hidden" name="okno" value="1">
	<span class="levySl">Platnost od:</span> <input type="text" name="platnost" size="15" maxlength="15" value="<?php echo $_POST["platnost"];?>"> 
	Archiv: <input type="checkbox" name="archivView" <?php echo (($_POST["archivView"])?"checked":""); ?>>
	<br>
	<span class="levySl">Hledat výraz:</span> <input class="enter" type="text" name="najit" size="20" maxlength="20" value="<?php echo (isset($_POST["najit"])?$_POST["najit"]:""); ?>"> <input type="submit" name="akce" value="Najít"> <span class="pozn">(hledá v polích èíslo smìrnice, revize, název a poznámka)</span>
	</form><br>
	<table id="seznam" cellpadding="3" cellspacing="0">
	<thead class=HlTab><td></td><td width="80">Èíslo</td><td width="90">revize</td><td>název</td><td width="80">platnost od</td><td>do</td><td>poznamka</td></thead>
	<?php 
		$Sql  = "SELECT * FROM smernice ";
		$podminka = "1";
		if (!empty($_POST["platnost"]))
			$podminka .= " and plat_od >= '".DateCzEn($_POST["platnost"])."'";
		if (!empty($_POST["najit"])) {
			$podminka .= " and (cislo LIKE '%".$_POST["najit"]."%' ";
			$podminka .= "or revize LIKE '%".$_POST["najit"]."%' ";
			$podminka .= "or nazev LIKE '%".$_POST["najit"]."%' ";
			$podminka .= "or poznamka LIKE '%".$_POST["najit"]."%') ";
		} 
		$Sql .= "WHERE archiv = '".(($_POST["archivView"])?"1":"0")."' and (($podminka) or cislo in (select cislo from smernice where $podminka))";
		$Sql .= " ORDER BY cislo";
		$seznam=mysql_query($Sql); //echo $Sql; 
		$cisloSmernice = "";
		while($radek = @mysql_fetch_assoc($seznam)) {
			if ($zmeny)
				echo "<TR id=\"s".$radek["id_smernice"]."\"";
			else
				echo "<TR ";
			if ($cisloSmernice<>$radek["cislo"])
				echo " class=\"suda\">";
			else
				echo " >";
			echo "<td>".((!empty($radek["soubor"]))?"<a href=\"#\" id=\"z".$radek["id_smernice"]."\" class=\"zobrazit\"><img src=\"img/".strtolower(substr(strchr($radek["soubor"],"."),1,3)).".gif\" border=\"0\"></a>":"")."</td><td>".(($cisloSmernice<>$radek["cislo"])?$radek["cislo"]:"")."</td><td>".$radek["revize"]."</td><td>".$radek["nazev"]."</td><td>".DateEnCz($radek["plat_od"])."</td><td>".$radek["plat_do"]."</td><td>".$radek["poznamka"]."</td>";
			if ($cisloSmernice<>$radek["cislo"])
				$cisloSmernice=$radek["cislo"];
			echo "</tr>\n";
		} 
	?>
	</table>
</div>
</body>
</html>
