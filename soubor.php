<?php
if (!isset($_GET["modul"]) || !isset($_GET["path"]) || !isset($_GET["file"])) 
	exit;
include "over.php"; 
if (empty($_GET["modul"])) {
	Header("Location: index.php");
	exit;
}
$result = mysqli_query($link,"SELECT * FROM menu_moduly WHERE id_modulu='".$_GET["modul"]."'");
if ($radek = mysqli_fetch_assoc($result)) {
	$path  = str_replace("..", "", $_GET["path"]);
	$cesta = "/share-new/".$radek["adresar"].$path;
} else {
	$cesta = "zadna";
}
$soubor=trim($_GET["file"]);
$pripona = strtolower(pathinfo($soubor, PATHINFO_EXTENSION));
switch ($pripona) {
case "htm":
case "html": $mimetype = "text/$pripona"; break;
default:
	$mimetype = "application/$pripona";
}

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

