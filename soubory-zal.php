<!DOCTYPE html>
<html dir="ltr" lang="cs"> 
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Intranet SSOK</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="css/stylesheet.css" rel="stylesheet">
	<script src="js/jquery-2.1.1.min.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<script src="js/common.js" type="text/javascript"></script>
	<link href="images/logo.png" rel="icon" />
</head>
<body>
    <div class="navbar">
 		<div class="col-sm-4"><img class="img-responsive" src="images/logoSede.png" alt="Správa silnic Olomouckého kraje" border="0"></div>
		<div class="col-sm-4 center break"><div class="f21">sobota 16.04.2021 12:45</div><div class="f15">svátek má Karel</div></div>
		<div class="col-sm-4 center"><i class="fa fa-user-circle"></i> <span class="f21">Jarmila Kopečná</span></div>
    </div>
    <div class="panel-body">
	    <aside class="col-sm-3 menu">
			<h2>Nabídka</h2>
			<a href="">1. Úvodní strana</a>
			<a href="">2. Dokumenty SSOK</a>
			<a href="">3. Interní dokumenty organizace</a>
			<a href="">4. Smlouvy, registry</a>
			<a href="">5. Zákony</a>
			<a href="">6. Procesy SSOK</a>
			<a href="">7. Rekreační péče</a>
			<a href="">8. Telefonní seznam</a>
			<a href="">9. Aktuality</a>
			<a href="">10. Výběrová řízení</a>
			<a href="">11. Smluvní partneři</a>
			<a href="">12. Smlouvy</a>
			<a href="">13. Bezpečnost práce</a>
			<a href="">14. Portál PO</a>
			<a href="">15. GDPR</a>
	    </aside>
	    <div class="col-sm-9">
			<h2>Dokumenty SSOK</h2>
			<div class="obsah">
				<p>Kliknutím na záhlaví sloupce dojde s seřazení záznamů dle příslušného sloupce. V poli vyhledat je možné zadat vyhledávací řětezce, kterými mohou být části názvu hledaného souboru oddělené mezerou. Při zadání více řetězců program hledá současný výskyt všech uvedených řetezců v kterékoliv části názvu souboru. Vyhledávání probíhá vždy pouze ve vybraném adresáři a jeho podadresářích.</p>
				<div class="hledat">
					<INPUT class="form-control" name="najit" type="text" value="" placeholder="Hledaný text ...">
					<span class="input-group-btn">
	    				<button type="button" class="btn btn-default btn-lg"><i class="fa fa-search"></i></button>
	  				</span>
				</div>
				<div class="table-responsive">
		        	<table class="table">
			            <thead>
			              <tr>
			                <td class="text-left" colspan="2">Název souboru</td>
			                <td class="text-left">datum</td>
			                <td class="text-right">velikost</td>
			              </tr>
			            </thead>
						<tbody>
							<?php obsahAdr("/share/intranet/gdpr","","") ?>
						</tbody>
					</table>
				</div>
			</div>
	    </div>
	</div>
    <div class="footer">
    </div>
</body>
</html>
<?php
function obsahAdr($dir,$podle,$smer,$najit="",$selekce="") {
	global $i,$Nazev,$Cesta,$Zmena,$Velikost;
	if (!isset($i)) {
		$Nazev = $Cesta = $Zmena = $Velikost = array();
		$i=0;
	}
    if (is_dir($dir) && false!==($handle=opendir($dir))) { 
		while (false!==($file = readdir($handle))) { echo $file."*";
		   if (povoleny($file,$dir)) {
		   	   if (!empty($najit) and is_dir($dir."/".$file)) 
				   obsahAdr($dir."/".$file,"","nepis",$najit); 
		  	   if (empty($najit) or eregi($najit,$file)) {
			       $Nazev[$i] = $file;
			       $Zmena[$i] = date("Y-m-d",filemtime($dir."/".$file));
				   $Cesta[$i] = $dir."/";
				   if (is_file($dir."/".$file)) {
				       $Velikost[$i] = filesize($dir."/".$file);
				   } else
					   $Velikost[$i] = "";
				   $i += 1;
			   }
		   }
		}
		closedir($handle); 
		if ($smer == "nepis")
			return;
		if ($podle == 1) {
			natcasesort($Nazev);
			if ($smer == ' asc')
				$Serazene= $Nazev;
			else
				$Serazene= array_reverse($Nazev,true);
		} else {
			natcasesort($Zmena);
			if ($smer == ' asc')
				$Serazene= $Zmena;
			else
				$Serazene= array_reverse($Zmena,true);
		}
		$i=0;
		if ((substr($dir,0,8) == "/share/R" && substr_count($dir,"/")>2) || substr_count($dir,"/")>3) {
			   echo "<tr><td>";
			   echo "<img src=\"img/updir.gif\" border=\"0\"> ";
			   echo "..</td><td></td><td align=\"right\"></td></tr>\n";
		  	   $i += 1;
		}
		while (list ($key, $value) = each ($Serazene)) { 
			if (strrpos(strrchr($dir, "/"),"(dle strediska)")==false || empty($_SESSION["dleStrediska"]) || strpos($Nazev[$key],$_SESSION["dleStrediska"]) || strpos($Nazev[$key],"reditelstvi")) {		
			    if ($i%2==0)
				   echo "<tr><td name=\"".(isset($Cesta[$i])?$Cesta[$i]:"")."\">";
				else
				   echo "<tr class=suda><td name=\"".(isset($Cesta[$i])?$Cesta[$i]:"")."\">";
				if ($podle == 1) {
				    echo "<img src=\"img/".obrazek($dir."/".$value).".gif\" border=\"0\"> ";
				    echo "$value</td><td>".date("d.m.Y",strtotime($Zmena[$key]))."</td><td align=\"right\">".$Velikost[$key]."</td></tr>\n";
				} else {
				    echo "<img src=\"img/".obrazek($dir."/".$Nazev[$key]).".gif\" border=\"0\"> ";
				    echo $Nazev[$key]."</td><td>".date("d.m.Y",strtotime($value))."</td><td align=\"right\">".$Velikost[$key]."</td></tr>\n";
				}
		  	    $i += 1;
			}
		}
 	} 
}
function povoleny($file,$dir) {
	if ($file == "." || $file == "..")
		return false;
	if (is_dir($dir."/".$file))
        return true;

	$pripony = array("doc","dot","xls","xlsx","docx","jpg", "gif", "tif", "txt","zip","pdf","htm","html","rar");
	$extension = pathinfo($file, PATHINFO_EXTENSION);
	return (in_array($extension, $pripony));
}

?>
