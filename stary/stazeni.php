<?php
readfile("http://www.ssok.cz/hlava.php?strana=stazeni&title=soubory_ke_stažení"); 
?> 
	<div class="hlavniOkno">
<?php
function zobraz($path,$dir) {
		$handleSubDir=opendir($path."/".$dir);
		while (false!==($file = readdir($handleSubDir))) {
			if ($file != "." && $file != "..") 
		   		if (is_dir($path."/".$dir."/".$file)) {
			   		echo "<h5 class=\"rozbal\">$file</h5>\n";
					echo "<div class=\"odsadit\">";
					zobraz($path,$dir."/".$file);
					echo "</div>";
				} else
					if (preg_match("/\.(xls|doc|pdf|txt|xlsx|docx|ppt|zip|rar)$/i", $file))
			   			echo "<a href=\"http://intranet.ssok.cz/soubor-web.php?adresar=verejne&file=$file&soubor=".("/".$dir."/".$file)."\" target=\"_blank\"><img src=\"http://intranet.ssok.cz/img/".preg_replace('/^.*\./', '',$file).".gif\" alt=\"\" border=\"0\"> $file (".ceil(filesize($path."/".$dir."/".$file)/1000)."kB)</a><br /><br />";
		}
		closedir($handleSubDir); 
}

		$path = "/share/intranet/verejne";
		$handle=opendir($path);
		while (false!==($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
		   		if (is_dir($path."/".$file)) {
			   		echo "<h3 class=\"rozbal\"><img src=\"http://intranet.ssok.cz/img/rozbal.jpg\" alt=\"\" border=\"0\"> $file</h3>\n";
					echo "<div class=\"odsadit\">";
					zobraz($path,$file);
					echo "</div>";
				}
			}
		}
		closedir($handle); 
?> 
	</div>		
<?php readfile("http://www.ssok.cz/pata.html");  ?> 