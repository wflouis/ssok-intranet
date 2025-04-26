<aside id="menu" class="menu">
	<!-- <div class="menu-background"></div> -->
	<h2>Nabídka</h2>
	<?php
		$result = mysqli_query($link,"SELECT menu_moduly.id_modulu, menu_moduly.popis, menu_moduly.odkaz, menu_moduly.poradi FROM menu_moduly
		LEFT JOIN opravneni on opravneni.id_modulu = menu_moduly.modul
		where opravneni.id_jmeno = {$_SESSION['id_jmeno']} or menu_moduly.modul = 0
		ORDER BY poradi ASC");
		while ($radek = mysqli_fetch_assoc($result)) {
			echo "<a ".((isset($_GET["modul"]) and $radek["id_modulu"]==$_GET["modul"])?"class=\"active\" ":"")."href=\"".$radek["odkaz"]."?modul=".$radek["id_modulu"]."\">".$radek["poradi"].". ".$radek["popis"]."</a>\n";
		}
	?>
</aside>