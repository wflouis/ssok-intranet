<div class="ouska">
	<ul>
		<li <?php echo ($_SESSION["okno"]==1)?"class=\"aktivni\"":"";?> id="ousko1">Sm�rnice</li>
		<li <?php echo ($_SESSION["okno"]==2)?"class=\"aktivni\"":"";?>id="ousko2">Zm�ny</li>
	</ul>
</div>
<div id="okraj"></div>
<div id="oknoDetail">
	<form action="smerniceNew.php" method="post" enctype="multipart/form-data" name="formDetail" id="formDetail">
	<input type="hidden" name="okno" value="2">
	<input type="hidden" name="najit" value="<?php echo $_POST["najit"];?>">
	<input type="hidden" name="id_smernice" value="<?php echo $_POST["id_smernice"];?>">
	
	<span class="levySl">��slo m�rnice:</span> <input type="text" name="cislo" size="15" maxlength="15" value="<?php echo $_POST["cislo"];?>"> 
	Revize: <input type="text" name="revize" size="15" maxlength="15" value="<?php echo $_POST["revize"];?>">
	Archiv: <input type="checkbox" name="archiv" <?php echo (($_POST["archiv"])?"checked":""); ?>>
	<br>
	<span class="levySl">Platnost od:</span> <input type="text" name="plat_od" size="15" maxlength="15" value="<?php echo $_POST["plat_od"];?>"> 
	do: <input type="text" name="plat_do" size="15" maxlength="15" value="<?php echo $_POST["plat_do"];?>">
	<br>
	<span class="levySl">N�zev:</span> <textarea cols="55" rows="3" name="nazev"><?php echo $_POST["nazev"];?></textarea><br>
	<span class="levySl">Soubor:</span> <input type="file" name="soubor" size="50" value="<?php echo $_POST["soubor"];?>"> <?php echo ((!empty($radek["soubor"]))?"<a href=\"#\" id=\"z".$radek["id_smernice"]."\" class=\"zobrazit\"><img src=\"img/".strtolower(substr(strchr($radek["soubor"],"."),1,3)).".gif\" border=\"0\"></a>":"")?><br>
	<span class="levySl">Pozn�mka:</span> <textarea cols="55" rows="3" name="poznamka"><?php echo $_POST["poznamka"];?></textarea><br><br>
	<span class="levySl">&nbsp;</span> <input type="submit" name="akce" value="Ulo�it"><input type="submit" name="akce" value="Nov� sm�rnice"><input type="submit" name="akce" value="Smazat" onClick="return confirm('Opravdu chcete smazat vybranou sm�rnici?');">
	</form>
</div>
