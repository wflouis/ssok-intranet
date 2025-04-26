<div class=vlevo>
	<div class=levySl>Jméno:</div><input type="text" name="jmeno" size="40" maxlength="40" value="<?php echo isset($_POST["jmeno"])?$_POST["jmeno"]:""; ?>"><br>
	<div class=levySl>Funkce:</div><input type="text" name="funkce" size="40" maxlength="40" value="<?php echo isset($_POST["funkce"])?$_POST["funkce"]:""; ?>"><br>
	<div class=levySl>Telefon:</div><input type="text" class=cislo name="telefon" size="11" maxlength="11" value="<?php echo isset($_POST["telefon"])?$_POST["telefon"]:""; ?>"> 
	Mobil: <input type="text" class=cislo name="mobil" size="11" maxlength="11" value="<?php echo isset($_POST["mobil"])?$_POST["mobil"]:""; ?>"><br>
	<div class=levySl>E-mail:</div><input type="text" name="email" size="40" maxlength="40" value="<?php echo isset($_POST["email"])?$_POST["email"]:""; ?>"> <br><br>
	<div class=levySl></div><input type="submit" name="akce" value="Uložit"> <input type="submit" name="akce" value="Nový"> <input type="button" id=akce name="akce" value="Smazat"> <input type="submit" name="akce" value="Zmìna støedisek"><br>
	<div class=levySl></div><input type="checkbox" name="internet" value="1" <?php echo ($_POST["internet"])?"checked":""; ?>> Zveøejnit na Internetu <br>
</div>
<div class=stredSl></div>
<div class=vlevo>Uživatelská práva pro zmìny:<br><br>
<select name="moduly[]" size="7" multiple>
<?php $result=mysqli_query($_SESSION["link"],"SELECT * FROM moduly ");
	while ($radek = mysqli_fetch_assoc($result)) {
		echo "<option value=\"".$radek["id_modulu"]."\" ".((array_search($radek["id_modulu"],$_POST["moduly"])===false)?"":" selected").">".$radek["popis"]."</option>\n";
	} 
?>
</select></div>
