<div class="ouska">
	<ul>
		<li <?php echo (!$detail)?"class=\"aktivni\"":"";?> id="ousko1">Partneøi</li>
		<li <?php echo ($detail)?"class=\"aktivni\"":"";?>id="ousko2">Zmìny</li>
	</ul>
</div>
<div id="okraj"></div>
<div id="oknoDetail">
	<form action="partneri.php" method="post" name="formDetail" id="formDetail">
	
	<input type="hidden" name="raditPodle" value="<?php echo $_POST["raditPodle"];?>">
	<input type="hidden" name="zmeny" value="1">
	<input type="hidden" name="najit" value="<?php echo $_POST["najit"];?>">
	<input type="hidden" name="id_partnera" value="<?php echo $_POST["id_partnera"];?>">
	
	<span class="levySl">IÈ:</span> <input type="text" name="ico" size="8" maxlength="8" value="<?php echo $_POST["ico"];?>"><br>
	<span class="levySl">Název:</span> <input type="text" name="nazev" size="50" maxlength="60" value="<?php echo $_POST["nazev"];?>"><br>
	<span class="levySl">Ulice:</span> <input type="text" name="ulice" size="30" maxlength="30" value="<?php echo $_POST["ulice"];?>"><br>
	<span class="levySl">PSÈ:</span> <input class="cislo" type="text" name="psc" size="5" maxlength="5" value="<?php echo $_POST["psc"];?>"> mìsto: <input type="text" name="mesto" size="30" maxlength="30" value="<?php echo $_POST["mesto"];?>"><br>

	<span class="levySl">Kontaktní osoba:</span> <input type="text" name="osoba" size="50" maxlength="50" value="<?php echo $_POST["osoba"];?>"><br>
	<span class="levySl">Kontaktní adresa:</span> <input type="text" name="kadresa" size="50" maxlength="50" value="<?php echo $_POST["kadresa"];?>"><br>
	<span class="levySl">Telefon:</span> <input class="cislo" type="text" name="telefon" size="14" maxlength="14" value="<?php echo $_POST["telefon"];?>"> e-mail: <input type="text" name="email" size="30" maxlength="30" value="<?php echo $_POST["email"];?>"> <br><br>
	<span class="levySl">&nbsp;</span> <input type="submit" name="akce" value="Uložit"><input type="submit" name="akce" value="Nový partner"><input type="submit" name="akce" value="Smazat">
	<script>
		if (window.opener!=undefined) {
			document.writeln("<input type=\"submit\" name=\"akce\" value=\"Pøevzít do smlouvy\" onClick=\"preved()\">");
		}
	</script>
	</form>
</div>
