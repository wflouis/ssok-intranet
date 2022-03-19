	    <aside class="menu">
			<h2>Nabídka</h2>
			<?php
  			$result = mysqli_query($link,"SELECT * FROM menu_moduly ORDER BY poradi ASC ");
			while ($radek = mysqli_fetch_assoc($result)) {
				echo "<a ".((isset($_GET["modul"]) and $radek["id_modulu"]==$_GET["modul"])?"class=\"active\" ":"")."href=\"".$radek["odkaz"]."?modul=".$radek["id_modulu"]."\">".$radek["poradi"].". ".$radek["popis"]."</a>\n";
			}
			?>
	    </aside>