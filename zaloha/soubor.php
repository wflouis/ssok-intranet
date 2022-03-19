<?php
//include "funkce/databaze.php"; 
//include "funkce/funkce.php"; 

//if ($_GET["modul"]!="verejne" && $_GET["modul"]!="rop" && $_GET["modul"]!="projekty" && !maPristup()) 
$seznamModulu = array("verejne","rop","projekty");
if (!isset($_GET["modul"]) || !isset($_GET["path"]) || !isset($_GET["file"])) 
	exit;
$soubor=trim($_GET["file"]);
$pripona = strtolower(pathinfo($soubor, PATHINFO_EXTENSION));
switch ($pripona) {
case "htm":
case "html": $mimetype = "text/$pripona"; break;
default:
	$mimetype = "application/$pripona";
}
$modul = $_GET["modul"];
$path  = str_replace("..", "", $_GET["path"]);
$cesta = "zadna";
$cesta = "/mnt/tonda/".$path;	

//print_r($_GET); echo "<br>$soubor<br>$adresar<br>$cesta";
if (is_file("$cesta/$soubor")) { 
    header('Content-type: $mimetype');
	header('Pragma: anytextexeptno-cache', true);
	header('Expires: ' . gmdate('D, d M Y H:i:s') . '  GMT');
	header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . '  GMT');
	header("Cache-Control: must-revalidate");
	header("Content-Length: ".filesize("$cesta/$soubor"));
	header("Content-disposition: inline; filename=\"".$soubor."\"");
	header("Accept-Ranges: ".filesize("$cesta/$soubor")); 
	readfile("$cesta/$soubor");
	exit;
}
?>
<!DOCTYPE html>
<html dir="ltr" lang="cs"> 
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
</head>
<body leftmargin="20" topmargin="20" bottommargin="20" rightmargin="20">
<h1>Soubor nenalezen!</h1>
<p>Vybraný soubor nebyl nalezen. Zkuste provést výběr znovu. Pokud se opět nepodaří soubor zobrazit, kontaktujte správce této aplikace k vyřešení tohoto problému.</p>
</body>
</html>

