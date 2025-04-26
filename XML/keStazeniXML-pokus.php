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
?>
<?php
  header('Expires: ' . gmdate('D, d M Y H:i:s') . '  GMT');
  header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . '  GMT');
  header('Content-Type: text/xml; charset=windows-1250');
?>
<?xml version="1.0" encoding="utf-8"?>
<?xml-stylesheet type="text/css" href="xml.css"?>
<ke_stazeni>
<?php
$path = "/share/intranet/verejne";
if ($handle=opendir($path)) {
	while (false!==($file = readdir($handle))) {
		if ($file != "." && $file != "..") {
	   		if (is_dir($path."/".$file)) {
		   		echo "\t<hlavni_slozka>\n";
				echo "\t\t<nazev>$file</nazev>\n";
				zobraz($path,"/".$file);
		   		echo "\t</hlavni_slozka>\n";
			}
		}
	}
	closedir($handle); 
}

function zobraz($path,$dir) {
	if ($handleSubDir=opendir($path.$dir)) {
		while (false!==($file = readdir($handleSubDir))) {
			if ($file != "." && $file != "..") 
		   		if (is_dir($path.$dir."/".$file)) {
			   		echo "\t\t<slozka>\n";
					echo "\t\t<nazev>$file</nazev>\n";
					zobraz($path,$dir."/".$file);
					echo "\t\t</slozka>\n";
				} else
					if (preg_match("/\.(xls|doc|pdf|txt|xlsx|docx|ppt|zip|rar)$/i", $file)) {
//			   			echo "\t\t<soubor href=\"http://intranet.ssok.cz/soubor-web.php?adresar=verejne&amp;soubor=".(urlencode($dir)."/".urlencode($file))."\" target=\"_blank\">$file (".ceil(filesize($path.$dir."/".$file)/1000)."kB)</soubor>\n";
			   			echo "\t\t<soubor><a href=\"http://intranet.ssok.cz/soubor-web.php?adresar=verejne&amp;soubor=".(urlencode($dir)."/".urlencode($file))."\" target=\"_blank\"><img src=\"http://intranet.ssok.cz/img/".preg_replace('/^.*\./', '',$file).".gif\" alt=\"\" border=\"0\" />$file (".ceil(filesize($path.$dir."/".$file)/1000)."kB)</a></soubor>\n";
				}
		}
		closedir($handleSubDir); 
	}
}
?>
</ke_stazeni>