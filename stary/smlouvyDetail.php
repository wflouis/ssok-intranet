<div class="ouska">
	<ul>
		<li <?php echo (!$detail)?"class=\"aktivni\"":"";?> id="ousko1">Smlouvy</li>
		<li <?php echo ($detail)?"class=\"aktivni\"":"";?>id="ousko2">Zmìny</li>
	</ul>
</div>
<div id="okraj"></div>
<div id="oknoDetail">
	<form action="smlouvy.php" method="post" enctype="multipart/form-data" name="formDetail" id="formDetail">
	
	<input type="hidden" name="raditPodle" value="<?php echo $_POST["raditPodle"];?>">
	<input type="hidden" name="zmeny" value="<?php echo $_POST["zmeny"];?>">
	<input type="hidden" name="najit" value="<?php echo $_POST["najit"];?>">
	<input type="hidden" name="rok" value="<?php echo $_POST["rok"];?>">
	<input type="hidden" name="id_smlouvy" value="<?php echo $_POST["id_smlouvy"];?>">
	
	<span class="levySl">Typ smlouvy:</span> 
	<select name="typSmlouvy" onChange="">
	<?php $result = mysqli_query($_SESSION["link"],"SELECT * FROM typySmluv WHERE 1=1");
		while ($radek = mysqli_fetch_assoc($result))
			echo "<option value=\"".$radek["id_typuSmlouvy"]."\" ".(($radek["id_typuSmlouvy"]==$_POST["typSmlouvy"])?" selected":"").">".$radek["popis"]."</option>";
	?>
	</select>
	støedisko: <select name="stredisko" onChange="">
	<?php $result = mysqli_query($_SESSION["link"],"SELECT * FROM seznam_str WHERE hlavni = '1'");
		while ($radek = mysqli_fetch_assoc($result))
			echo "<option value=\"".$radek["id_str"]."\"".(($radek["id_str"]==$_POST["stredisko"])?" selected":"").">".$radek["nazev"]."</option>";
	?>
	</select><br>
	<span class="levySl">Èíslo smlouvy:</span> <input type="text" name="cisloSmlouvy" size="20" maxlength="20" value="<?php echo $_POST["cisloSmlouvy"];?>"> <?php echo (($_POST["velikost"]>0)?"<img src=\"img/".strtolower(substr(strchr($_POST["soubor"],"."),1,3)).".gif\" border=\"0\"> ":"").$_POST["velikost"]." kB";?> - naèíst z: <input type="file" name="soubor" size="30" value="<?php echo $_POST["soubor"];?>"> <br>
	<span class="levySl">Datum uzavøení:</span> <input class="cislo" type="text" name="datumUzavreni" size="10" maxlength="10" value="<?php echo $_POST["datumUzavreni"];?>"> cena: <input type="text" name="cena" size="30" maxlength="30" value="<?php echo $_POST["cena"];?>"> <br>
	<span class="levySl">Smluvní strana (IÈ):</span> <input class="cislo" type="text" name="ico" size="10" maxlength="8" value="<?php echo $_POST["ico"];?>"> <input type="button" name="partneri ..." value="Partneøi" onclick="window.open('partneri.php'); return false"><span id="firma"><?php echo $_POST["nazev"].", ".$_POST["ulice"].", ".$_POST["psc"]." ".$_POST["mesto"];?></span><br>
	<span class="levySl">Fyzická osoba:</span> <input type="text" name="rodneCislo" size="100" maxlength="100" value="<?php echo $_POST["rodneCislo"];?>"><br>
	<span class="levySl">Pøedmìt smlouvy:</span> <textarea cols="60" rows="3" name="predmet"><?php echo $_POST["predmet"];?></textarea><br>
	<span class="levySl">Doba trvání:</span> <input type="text" name="datumTxt" size="30" maxlength="30" value="<?php echo $_POST["datumTxt"];?>"> od: <input type="text" class="cislo" name="datumOd" size="10" maxlength="10" value="<?php echo $_POST["datumOd"];?>"> do: <input type="text" class="cislo" name="datumDo" size="10" maxlength="10" value="<?php echo $_POST["datumDo"];?>"><br>
	<span class="levySl">Pøipomenout:</span> <input type="checkbox" name="upozornit" <?php echo (($_POST["upozornit"])?"checked":""); ?>> kdy: <input type="text" name="kdy" size="10" maxlength="10" value="<?php echo $_POST["kdy"];?>"> text: <input type="text" name="text" size="50" maxlength="50" value="<?php echo $_POST["text"];?>"><br>
	<span class="levySl">Uhrazeno dne:</span> <input class="cislo" type="text" name="uhrazeno" size="10" maxlength="10" value="<?php echo $_POST["uhrazeno"];?>"> Faktura: <input type="text" name="faktura" size="15" maxlength="15" value="<?php echo $_POST["faktura"];?>"><br><br>
	<span class="levySl">&nbsp;</span> <input type="submit" name="akce" value="Uložit"><input type="submit" name="akce" value="Nová smlouva"><input type="submit" name="akce" value="Smazat">
	</form>
</div>
