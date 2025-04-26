<?php
	$homePage = true;

	include "over.php";
	include 'souborypath.php';

	$writePermission = strpos($_SESSION['prava'], 'D') !== false;

	if (empty($_GET["modul"])) {
		Header("Location: index.php");
		exit;
	}

	$stmt = mysqli_prepare($link, "SELECT menu_moduly.popis, adresar, odkaz FROM menu_moduly
	LEFT JOIN opravneni on opravneni.id_modulu = menu_moduly.modul
	where (opravneni.id_jmeno = {$_SESSION['id_jmeno']} or menu_moduly.modul = 0)
		and menu_moduly.id_modulu = ?
	ORDER BY poradi ASC
	");
	echo mysqli_error($link);
	$stmt->bind_param('i', $_GET['modul']);
	$stmt->execute();
	echo $stmt->error;
	$result = $stmt->get_result();

	if ($radek = mysqli_fetch_assoc($result)) {
		$basePath = '/test_soubory/';
	        $basePath = '/' . $radek["adresar"];
		$modulPath = $souboryPath . $basePath;
		$title = $radek['popis'];
	} else {
		$modulPath = "";
	}

	if ($radek["odkaz"] != 'soubory.php') {
		Header("Location: index.php");
		exit;
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
		"url"  => 'html.png',
		"rar"  => 'pack.png',
		"zip"  => 'pack.png',
		"mov"  => 'mov.png',
		"avi"  => 'mov.png',
		"mp4"  => 'mov.png',
		"webm"  => 'mov.png',
		"mts"=> 'mov.png'
	);
	include "hlava.php";
	include "nabidka.php";
?>

<div>
	<h2 class="obsah-title"><?=$title?></h2>
	<div class="obsah">
		<p class="search-text">Kliknutím na záhlaví sloupce dojde k seřazení záznamů dle příslušného sloupce.<br>
			V poli vyhledat je možné zadat vyhledávací řetezce, kterými mohou být části názvu hledaného souboru oddělené mezerou.<br>
			Při zadání více řetězců program hledá současný výskyt všech uvedených řetezců v kterékoliv části názvu souboru.<br>
			Vyhledávání probíhá vždy pouze ve vybraném adresáři a jeho podadresářích.</p>
		<form id="hledat" action="soubory.php" method="get">
			<input name="modul" type="hidden" value="<?=$_GET["modul"]?>">
			<input name="path" type="hidden" value="<?=$path?>">
			<div class="flex flex-center-v">
				<div class='flex soubory-search-container'>
					<input class="txt" name="najit" type="text" placeholder="Hledaný text ..." autofocus>
					<button class="btn btn-width"><i class="fa fa-search"></i></button>
				</div>
				<?php
					if(strpos($_SESSION['prava'], 'D') !== false){
						echo '
						<label title="Vložit soubor" class="txt button icon input-file-icon">
							<input type="file" multiple oninput="uploadFiles(event)">
						</label>
						<div>
							<input style="margin-left:20px" id="create_aktualita" type="checkbox"></input>
							<label for="create_aktualita">Vytvořit aktualitu</label>
						</div>
						';
					}
				?>
			</div>
			<div class='gap'></div>
		</form>
		<script>
			let dirPath = '<?=$basePath . $path?>/'
			function uploadFiles(e){
				let createAktualita = document.getElementById('create_aktualita').checked
				
				let formData = new FormData();
				let length = e.target.files.length
				let filePaths = []

				for(let f of e.target.files){
					formData.append('files[]', f)
					filePaths.push(dirPath + "/" + f.name)
				}
				fetch('api/soubory/post.php?path=' + encodeURIComponent(dirPath) + "&createAktualita=" + (!!createAktualita).toString(), {
					method:'post',
					body:formData
				})
				.then(r => r.text())
				.then(r => {
					if(r != ''){
						alert(r)
						return
					}

					if(!createAktualita){
						if(confirm('Soubory (' + length + ') nahrány')){
							location.reload()
						}
						return
					}

					let formData = new FormData()
					formData.append('obj', JSON.stringify({
						text: "Byly nahrány nové soubory:\n\n" + filePaths.join('\n'),
					}))

					fetch('api/aktuality/post.php', {
						method:'post',
						body:formData
					})
					.finally(r => {
						if(confirm('Soubory (' + length + ') nahrány a aktualita vytvořena')){
							location.reload()
						}
					})
				})
				e.target.value = ''
			}

			function deleteFile(row, name){
				event.cancelBubble = true
				let path = (dirPath + name).replaceAll('//', '/')
				if(confirm('Smazat soubor: ' + path)){
					fetch('api/soubory/delete.php?path=' + encodeURIComponent(path), {method:'post'})
					.then(r => r.text())
					.then(r => {
						if(r != ''){
							alert(r)
							return
						}
						row.remove()
					})
				}
			}
			function redirectFolder(path){
				location.href = path
			}
		</script>
		<table id="folders" class="table">
			<thead>
				<tr path="soubory.php?modul=<?=$_GET["modul"]?>&path=<?=$path?>">
					<td class="<?=(($_SESSION["podle"]=="1")? (($_SESSION["smer"]%2==0)?"sort-desc":"sort-asc"):"")?>" colspan="2" sort="1">Název souboru</td>
					<td class="<?=(($_SESSION["podle"]=="2")? (($_SESSION["smer"]%2==0)?"sort-num-down":"sort-num-up"):"")?>" sort="2">Datum</td>
					<td class="text-right <?=(($_SESSION["podle"]=="3")? (($_SESSION["smer"]%2==0)?"sort-num-down":"sort-num-up"):"")?>" sort="3">Velikost</td>
					<?php if($writePermission) {
						echo '<td>Smazat</td>';
					} ?>
				</tr>
			</thead>
			<tbody>
				<?php obsahAdr($modulPath,$path,"1",$_SESSION["podle"],$najit) ?>
			</tbody>
		</table>
	</div>
</div>
<?php
include "pata.php";

function obsahAdr($modulPath,$path,$smer,$podle,$najit="",$selekce="") {
	global $icons, $Adresar, $AdresarCesta, $DatumAdresare, $Nazev, $Cesta, $DatumSouboru, $Velikost, $basePath, $writePermission;
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
					   $Cesta[] = $path;
					//    $Path[] =
				       $DatumSouboru[] = date("Y-m-d",filemtime($modulPath.$path."/".$file));
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
			   echo "<tr folder='true' onclick='redirectFolder(\""."soubory.php?modul=".$_GET["modul"]."&path=".substr($path,0,strrpos($path,"/"))."\")'><td>";
			   echo "<img src=\"images/updir.png\" border=\"0\"></td>";
			   echo "<td> ..</td><td></td><td align=\"right\"></td>";
				 if($writePermission) {
					 echo "<td></td>";
				 }
				 echo "</tr>";
		}
		foreach($sortAdresar as $key => $value) {
			if (strrpos(strrchr($path, "/"),"(dle strediska)")==false || empty($_SESSION["dleStrediska"]) || strpos($Adresar[$key],$_SESSION["dleStrediska"]) || strpos($Adresar[$key],"reditelstvi")) {
			    echo "<tr folder='true' onclick='redirectFolder(\""."soubory.php?modul=".$_GET["modul"]."&path=".$AdresarCesta[$key]."\")' path=\"soubory.php?modul=".$_GET["modul"]."&path=".$AdresarCesta[$key]."\"><td><img src=\"images/dir.png\" border=\"0\"></td><td>";
			    echo $Adresar[$key]."</td><td>".date("d.m.Y",strtotime($DatumAdresare[$key]))."</td><td align=\"right\"></td>";
					if($writePermission) {
						echo "<td></td>";
					}
					echo "</tr>";
			}
		}
		foreach($sortSoubor as $key => $value) {
			if (strrpos(strrchr($path, "/"),"(dle strediska)")==false || empty($_SESSION["dleStrediska"]) || strpos($Nazev[$key],$_SESSION["dleStrediska"]) || strpos($Nazev[$key],"reditelstvi")) {
		    echo "<tr onclick='downloadFile(\"api/soubory/readsoubor.php\", \"".$basePath . ($Cesta[$key] ?? $path) . '/' . $Nazev[$key]."\")'><td><img src=\"images/".$icons[strtolower(pathinfo($Nazev[$key], PATHINFO_EXTENSION))]."\" border=\"0\"></td><td class='table-filename'>";
		    echo $Nazev[$key]."</td><td>".date("d.m.Y",strtotime($DatumSouboru[$key]))."</td><td align=\"right\">".(round($Velikost[$key]/1000))." kB</td>";
				if($writePermission) {
					echo "<td onclick='deleteFile(this.parentNode, \"{$Nazev[$key]}\")'><a title='Smazat' class='icon td-xmark'></a></td>";
				}
				echo "</tr>";
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

	$pripony = array("doc","dot","docx","xls","xlsx","xltx","ppt","pptx","jpg", "gif", "bmp", "tif", "png", "txt","pdf","htm","html","url","rar","zip","mov","avi","mp4","webm","mts");
	$extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
	return (in_array($extension, $pripony));
}

?>
