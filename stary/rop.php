<?php 
readfile("http://www.ssok.cz/hlava.php?strana=rop&title=stavby_spolufinancovan�_EU"); 
?> 
	<div class="hlavniOkno">
		<img src="images/srop.jpg" alt="" border="0"><img src="images/rop.jpg" alt="" border="0"><br /><br />
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
			   			echo "<a href=\"http://intranet.ssok.cz/soubor.php?adresar=rop&file=$file&soubor=".("/".$dir."/".$file)."\" target=\"_blank\"><img src=\"http://intranet.ssok.cz/img/".preg_replace('/^.*\./', '',$file).".gif\" alt=\"\" border=\"0\"> $file (".ceil(filesize($path."/".$dir."/".$file)/1000)."kB)</a><br /><br />";
		}
		closedir($handleSubDir); 
}

		$path = "/share/intranet/rop";
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
		<h3>SROP</h3>
		<p>Spr�va silnic Olomouck�ho kraje podala v r�mci Spole�n�ho region�ln� opera�n�ho programu (SROP) 3 projekty na opravy a rekonstrukce silnic II. a III. t��d v Olomouck�m kraji.
		<p>Jedn� se o tyto projekty:
		<ul>
		<li><a href="srop1.php">Rekonstrukce komunikace III/44317 Hlubo�ky - term�n realizace: 01. 09.2005 - 31. 05.2006</a></li>
		<li><a href="srop2.php">Oprava silnice III/4496 Kr�lov� - �ervenka - term�n realizace: 01.08.2005 - 31.10.2005</a></li>
		<li><a href="srop3.php">II/438 Opatovice - B��kovice - hranice okr. PR/KM - oprava komunikace - term�n realizace: 01. 08. 2005 - 30. 09. 2005</a></li>
		</ul> 
	</div>
<?php readfile("http://www.ssok.cz/pata.html");  ?> 