<?php
$icons = array(
	"doc"  => 'word.png',
	"dot"  => 'word.png',
	"docx" => 'word.png',
	"xls"  => 'excel.png',
	"xlsx" => 'excel.png',
	"xltx" => 'excel.png',
	"jpg"  => 'img.png',
	"gif"  => 'img.png',
	"bmp"  => 'img.png',
	"tif"  => 'img.png',
	"png"  => 'img.png',
	"txt"  => 'txt.png',
	"pdf"  => 'pdf.png',
	"htm"  => 'html.png',
	"html" => 'html.png',
	"rar"  => 'pack.png',
	"zip"  => 'pack.png',
	"mov"  => 'mov.png',
	"avi"  => 'mov.png',
	"mp4"  => 'mov.png',
	"mts"=> 'mov.png'
);
header('Expires: ' . gmdate('D, d M Y H:i:s') . '  GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . '  GMT');
header('Content-Type: text/xml; charset=windows-1250');
//< ?xml-stylesheet type="text/css" href="xml.css"? >
//< ?xml-stylesheet type="text/xsl" href="keStazeni.xsl"? >
?>
<?xml version="1.0" encoding="utf-8"?>
<ke_stazeni>
<?php
$path = "/share/intranet/";
$adresar = "verejne";
$dir = "";
if (!empty($_GET["slozka"])) {
	switch ($_GET["slozka"]) {
	case "informace": $dir = urldecode("%2FInformace+dle+z%E1kona+%E8.+106-1999+Sb"); break;
	case "rop": 
		$adresar = "rop"; 
		echo "<obrazek href=\"http://intranet.ssok.cz/obrazky/srop.jpg\" text=\"\" />\n";
		echo "<obrazek href=\"http://intranet.ssok.cz/obrazky/rop.jpg\" text=\"\" />\n";
		break;
	case "projekty": 
		$path = "/share/"; $adresar = "projekty"; 
		echo "<obrazek href=\"http://intranet.ssok.cz/obrazky/EU.png\" text=\"\" />\n";
		echo "<obrazek href=\"http://intranet.ssok.cz/obrazky/mmr.png\" text=\"\" />\n";
   		echo "<slozka text=\"Realizované projekty\" />\n";
	}
} 
if ($handle=opendir($path.$adresar.$dir)) { 
	while (false!==($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
	   		if (is_dir($path.$adresar.$dir."/".$file)) {
		   		echo "<hlavni_slozka text=\"$file\">\n";
				zobraz($path,$adresar,$dir."/".$file);
		   		echo "</hlavni_slozka>\n";
			} else {
				if (preg_match("/\.(xls|doc|pdf|txt|xlsx|docx|ppt|zip|rar)$/i", $file)) {
		   			echo "<soubor href=\"http://intranet.ssok.cz/soubor-web.php?adresar=$adresar&amp;soubor=".(urlencode($dir)."/".urlencode($file))."\" text=\"$file (".ceil(filesize($path.$adresar.$dir."/".$file)/1000)."kB)\" />\n";
				}
			}
		}
	}
	closedir($handle); 
}

function zobraz($path,$adresar,$dir) { 
	if ($handleSubDir=opendir($path.$adresar.$dir)) {
		while (false!==($file = readdir($handleSubDir))) {
			if ($file != "." && $file != "..") {
		   		if (is_dir($path.$adresar.$dir."/".$file)) {
			   		echo "<slozka text=\"$file\">\n";
					zobraz($path,$adresar,$dir."/".$file);
					echo "</slozka>\n";
				} else {
					if (preg_match("/\.(xls|doc|pdf|txt|xlsx|docx|ppt|zip|rar)$/i", $file)) {
			   			echo "<soubor href=\"http://intranet.ssok.cz/soubor-web.php?adresar=$adresar&amp;soubor=".(urlencode($dir)."/".urlencode($file))."\" text=\"$file (".ceil(filesize($path.$adresar.$dir."/".$file)/1000)."kB)\" />\n";
					}
				}
			}
		}
		closedir($handleSubDir); 
	}
}
?>
</ke_stazeni>