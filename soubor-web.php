<?php
include "funkce/databaze.php"; 
include "funkce/funkce.php"; 

$mimetypes = array(
    'doc'        => 'application/msword',
    'docx'        => 'application/msword',
    'pdf'        => 'application/pdf',
    'xls'        => 'application/vnd.ms-excel',
    'xlsx'        => 'application/vnd.ms-excel',
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

if ($_GET["adresar"]!="verejne" && $_GET["adresar"]!="rop" && $_GET["adresar"]!="projekty" && !maPristup()) 
	exit;
$soubor=trim($_GET["soubor"]);
$adresar=$_GET["adresar"];
$seznamAdr=",predpisy,rejstrik,rekreace,seznam,procesy,bezpecnost,verejne,rop";
$cesta = "zadna";
switch (true) {
case substr($adresar,0,7) == "Registr" or $adresar == "projekty":
	$cesta = "/share";
	break;
case strpos($seznamAdr,substr($adresar,0,7)) > 0:
	$cesta = "/share/intranet";	
	break;
case substr($adresar,0,7) == "/share/":
	$adresar = substr($adresar,1,strlen($adresar)-2);
	$cesta = "";	
	break;
default:
}
$pracovni= md5(rand()).trim(substr($soubor,strrpos($soubor,"."),5));

function dircpy($source, $dest)
{
  if($handle = opendir($source)){ 
   if(!is_dir($dest))
	  mkdir($dest); 
   while(false !== ($file = readdir($handle))){ 
     if($file != '.' && $file != '..'){
       $path = $source . '/' . $file;
       if(is_file($path)){
         if(!is_file($dest . '/' . $file))
           if(!@copy($path, $dest . '/' . $file)){
             echo '<font color="red">Soubor ('.$path.') nejde otevøít.</font>';
           }
       } elseif(is_dir($path)){
         dircpy($path, $dest . '/' . $file); echo "Adresáø ".$path .  $dest . '/' . $file."vytvoøen";
       }
     }
   }
   closedir($handle);
  } 
} 

//print_r($_GET); echo "<br>$soubor<br>$adresar<br>$cesta";exit;
if (is_file("$cesta/$adresar/$soubor")) { 
	$bezPripony = trim(substr($soubor,0,strrpos($soubor,".")));
	$Pripona = strtolower(trim(substr($soubor,strrpos($soubor,".")+1,4)));
	if (is_dir("$cesta/$adresar/$bezPripony"."_soubory")) { 
		if (@copy("$cesta/$adresar/$soubor", "tmp/$pracovni")) {
			dircpy("$cesta/$adresar/$bezPripony"."_soubory", "tmp/$bezPripony"."_soubory");
			Header("Location: tmp/$pracovni");
		}
	} else { 

	    header('Content-type: '.$mimetypes[$Pripona]);
	    header('Content-Description: File Transfer');
//		header('Pragma: anytextexeptno-cache', true);
		header('Pragma: public');
//		header("Expires: Mon, 1 Jan 1900 00:00:00 GMT");
		header('Expires: 0');
//		header('Content-Type: application/octet-stream');
//		header('Content-Transfer-Encoding: binary');
//		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: must-revalidate");
//		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header("Content-Length: ".filesize("$cesta/$adresar/$soubor"));
//		header("Content-disposition: inline; filename=\"".$_GET["file"]."\"");
//		header("Content-disposition: inline; filename=\"".$soubor."\"");
		header("Content-Disposition: attachment; filename=\"".$soubor."\"");
//		header("Accept-Ranges: ".filesize("$cesta/$adresar/$soubor")); 
//		ob_clean();
//		flush();
		readfile("$cesta/$adresar/$soubor");
	}
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=windows-1250">	
	<LINK href="intranet.css" type=text/css rel=stylesheet>
</head>
<body leftmargin="20" topmargin="20" bottommargin="20" rightmargin="20">
<p class=N3>Soubor nenalezen!</p>
Vybraný soubor nebyl nalezen. Zkuste provést výbìr znovu. Pokud se opìt nepodaøí soubor zobrazit, kontaktujte správce této aplikace k vyøešení tohoto problému.
</body>
</html>

