<?php
	include "over.php"; 
	if (empty($_GET["modul"])) {
		Header("Location: index.php");
		exit;
	}
	$result = mysqli_query($link,"SELECT * FROM menu_moduly WHERE id_modulu='".$_GET["modul"]."'");
	if ($radek = mysqli_fetch_assoc($result)) {
		$modulPath = "/mnt/tonda/".$radek["adresar"]."/";
	} else {
		$modulPath = "";
	}
	$path = $najit = "";
	if (isset($_GET["najit"])) {
		$najit = preg_replace("/[^\p{L}\p{N}\s\.\-\_\+]/iu", '', $_GET["najit"]);
	}
	if (isset($_GET["path"])) {
		$path = str_replace("..", "", $_GET["path"]);
	}
	if (!isset($_GET["podle"])) {
		$_GET["podle"] = 1;
	} else {
		if ($_SESSION["podle"] == $_GET["podle"]) {
			$_SESSION["smer"]++;
		}
	}
	$_SESSION["podle"] = $_GET["podle"];
	if (!isset($_SESSION["smer"])) {
		$_SESSION["smer"] = 0;
	}
	$icons = array(
		"doc"  => 'word.png',
		"dot"  => 'word.png',
		"docx" => 'word.png',
		"xls"  => 'excel.png',
		"xlsx" => 'excel.png',
		"xltx" => 'excel.png',
		"ppt"  => 'ppt.png',
		"pptx" => 'ppt.png',
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
	include "hlava.php"; 
	include "nabidka.php"; 
?>

	    <div class="col-sm-9">
			<h2>Dokumenty SSOK</h2>
			<div class="obsah">
				<p>Kliknutím na záhlaví sloupce dojde k seřazení záznamů dle příslušného sloupce. V poli vyhledat je možné zadat vyhledávací řětezce, kterými mohou být části názvu hledaného souboru oddělené mezerou. Při zadání více řetězců program hledá současný výskyt všech uvedených řetezců v kterékoliv části názvu souboru. Vyhledávání probíhá vždy pouze ve vybraném adresáři a jeho podadresářích.</p>
				<form id="hledat" action="soubory.php" method="get">
				<div class="hledat">
					<INPUT name="modul" type="hidden" value="<?php echo $_GET["modul"]; ?>">
					<INPUT name="path" type="hidden" value="<?php echo $path; ?>">
					<INPUT class="form-control" name="najit" type="text" value="<?php echo $najit; ?>" placeholder="Hledaný text ...">
					<span class="input-group-btn">
	    				<button type="button" class="btn btn-default btn-lg"><i class="fa fa-search"></i></button>
	  				</span>
				</div>
				</form> 
				<div class="table-responsive">
		        	<table id="folders" class="table">
			            <thead>
			              <tr path="soubory.php?modul=<?php echo $_GET["modul"]; ?>&path=<?php echo $path; ?>">
			                <td class="text-left<?php echo (($_SESSION["podle"]=="1")? (($_SESSION["smer"]%2==0)?" sort-down":" sort-up"):""); ?>" colspan="2" sort="1">Název souboru</td>
			                <td class="text-left<?php echo (($_SESSION["podle"]=="2")? (($_SESSION["smer"]%2==0)?" sort-num-down":" sort-num-up"):""); ?>" sort="2">datum</td>
			                <td class="text-right<?php echo (($_SESSION["podle"]=="3")? (($_SESSION["smer"]%2==0)?" sort-num-down":" sort-num-up"):""); ?>" sort="3" width="80">velikost</td>
			              </tr>
			            </thead>
						<tbody>
							<?php obsahAdr($modulPath,$path,"1",$_SESSION["podle"],$najit) ?>
						</tbody>
					</table>
				</div>
			</div>
	    </div>
<?php
include "pata.php"; 

function obsahAdr($modulPath,$path,$smer,$podle,$najit="",$selekce="") {
	global $icons, $Adresar, $AdresarCesta, $DatumAdresare, $Nazev, $Cesta, $DatumSouboru, $Velikost;
	if (!isset($Adresar))
		$Adresar = $AdresarCesta = $DatumAdresare = $Nazev = $Cesta = $DatumSouboru = $Velikost = array();

    if (is_dir($modulPath.$path) && false!==($handle=opendir($modulPath.$path))) { 
		while (false!==($file = readdir($handle))) { 
		   if (povoleny($file,$modulPath.$path)) {
				if (is_dir($modulPath.$path."/".$file)) {
					if (!empty($najit))
						obsahAdr($modulPath,$path."/".$file,"zpet","",$najit);
					else {
						$Adresar[] = $file;
						$AdresarCesta[] = $path."/".$file;
						$DatumAdresare[] = date("Y-m-d",filemtime($modulPath.$path."/".$file));
					}
				} else {
					if (empty($najit) or najdi($najit,$file)) {
//				       $Nazev[] = iconv("WINDOWS-1250", "UTF-8", $file);
				       $Nazev[] = $file;
				       $DatumSouboru[] = date("Y-m-d",filemtime($modulPath.$path."/".$file));
					   $Cesta[] = $path;
				       $Velikost[] = filesize($modulPath.$path."/".$file);
					}
				}
			}
		}
		closedir($handle); 
		if ($smer == "zpet")
			return;
		switch ($podle) {
		case "1":
			natcasesort($Nazev);
			natcasesort($Adresar);
			$sortAdresar = $Adresar;
			$sortSoubor   = $Nazev;
			break;
		case "2":
			natcasesort($DatumAdresare);
			natcasesort($DatumSouboru);
			$sortAdresar = $DatumAdresare;
			$sortSoubor   = $DatumSouboru;
			break;
		case "3":
			natcasesort($Velikost);
			natcasesort($Adresar);
			$sortAdresar = $Adresar;
			$sortSoubor   = $Velikost;
		}
		if ($_SESSION["smer"]%2==0) {
			$sortAdresar = array_reverse($sortAdresar,true);
			$sortSoubor   = array_reverse($sortSoubor,true);
		}
		if (!empty($path) && substr_count($path,"/")>0) {
			   echo "<tr path=\"soubory.php?modul=".$_GET["modul"]."&path=".substr($path,0,strrpos($path,"/"))."\"><td>";
			   echo "<img src=\"images/updir.png\" border=\"0\"></td>";
			   echo "<td> ..</td><td></td><td align=\"right\"></td></tr>\n";
		}
		while (list ($key, $value) = each ($sortAdresar)) { 
			if (strrpos(strrchr($path, "/"),"(dle strediska)")==false || empty($_SESSION["dleStrediska"]) || strpos($Adresar[$key],$_SESSION["dleStrediska"]) || strpos($Adresar[$key],"reditelstvi")) {		
			    echo "<tr path=\"soubory.php?modul=".$_GET["modul"]."&path=".$AdresarCesta[$key]."\"><td><img src=\"images/dir.png\" border=\"0\"></td><td>";
			    echo $Adresar[$key]."</td><td>".date("d.m.Y",strtotime($DatumAdresare[$key]))."</td><td align=\"right\"></td></tr>\n";
			}
		}
		while (list ($key, $value) = each ($sortSoubor)) { 
			if (strrpos(strrchr($path, "/"),"(dle strediska)")==false || empty($_SESSION["dleStrediska"]) || strpos($Nazev[$key],$_SESSION["dleStrediska"]) || strpos($Nazev[$key],"reditelstvi")) {		
			    echo "<tr path=\"soubor.php?modul=".$_GET["modul"]."&path=".urlencode($Cesta[$key])."&file=".urlencode($Nazev[$key])."\"><td><img src=\"images/".$icons[strtolower(pathinfo($Nazev[$key], PATHINFO_EXTENSION))]."\" border=\"0\"></td><td>";
			    echo $Nazev[$key]."</td><td>".date("d.m.Y",strtotime($DatumSouboru[$key]))."</td><td align=\"right\">".(round($Velikost[$key]/1000))." kB</td></tr>\n";
			}
		}
 	} 
}
function najdi($co,$kde) {
	$hledat = explode(" ", $co);
	$nasel = true;
	foreach($hledat as $retezec) {
		if (!preg_match("/$retezec/iu",$kde)) {
			$nasel = false;
		}
	}
	return $nasel;
}
function povoleny($file,$dir) {
	if ($file == "." || $file == "..")
		return false;
	if (is_dir($dir."/".$file))
        return true;

	$pripony = array("doc","dot","docx","xls","xlsx","xltx","ppt","pptx","jpg", "gif", "bmp", "tif", "png", "txt","pdf","htm","html","rar","zip","mov","avi","mp4","mts");
	$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION)); 
	return (in_array($extension, $pripony));
}

?>
