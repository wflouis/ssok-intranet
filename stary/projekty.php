<?php
		$path = "/share/projekty";
		$handle=opendir($path); 
		while (false!==($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (preg_match("/\.(xls|doc|pdf|txt|xlsx|docx|ppt|zip|rar)$/i", $file)) { 
//		   			echo "<a href=\"http://intranet.ssok.cz/soubor.php?adresar=projekty&file=$file&soubor=".($file)."\" target=\"_blank\"><img src=\"http://intranet.ssok.cz/img/".preg_replace('/^.*\./', '',$file).".gif\" alt=\"\" border=\"0\"> $file (".ceil(filesize($path."/".$dir."/".$file)/1000)."kB)</a><br /><br />";
		   			echo "<a href=\"http://intranet.ssok.cz/soubor-web.php?adresar=projekty&file=$file&soubor=".($file)."\" target=\"_blank\"><img src=\"http://intranet.ssok.cz/img/".preg_replace('/^.*\./', '',$file).".gif\" alt=\"\" border=\"0\"> $file (".ceil(filesize($path."/".$file)/1000)."kB)</a><br /><br />";
		   		}
			}
		}
		closedir($handle); 
?> 
