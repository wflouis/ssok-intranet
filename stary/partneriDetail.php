<div class="ouska">
	<ul>
		<li <?php echo (!$detail)?"class=\"aktivni\"":"";?> id="ousko1">Partne�i</li>
		<li <?php echo ($detail)?"class=\"aktivni\"":"";?>id="ousko2">Zm�ny</li>
	</ul>
</div>
<div id="okraj"></div>
<div id="oknoDetail">
	<form action="partneri.php" method="post" name="formDetail" id="formDetail">
	
	<input type="hidden" name="raditPodle" value="<?php echo $_POST["raditPodle"];?>">
	<input type="hidden" name="zmeny" value="1">
	<input type="hidden" name="najit" value="<?php echo $_POST["najit"];?>">
	<input type="hidden" name="id_partnera" value="<?php echo $_POST["id_partnera"];?>">
	
	<span class="levySl">I�:</span> <input type="text" name="ico" size="8" maxlength="8" value="<?php echo $_POST["ico"];?>"><br>
	<span class="levySl">N�zev:</span> <input type="text" name="nazev" size="50" maxlength="60" value="<?php echo $_POST["nazev"];?>"><br>
	<span class="levySl">Ulice:</span> <input type="text" name="ulice" size="30" maxlength="30" value="<?php echo $_POST["ulice"];?>"><br>
	<span class="levySl">PS�:</span> <input class="cislo" type="text" name="psc" size="5" maxlength="5" value="<?php echo $_POST["psc"];?>"> m�sto: <input type="text" name="mesto" size="30" maxlength="30" value="<?php echo $_POST["mesto"];?>"><br>

	<span class="levySl">Kontaktn� osoba:</span> <input type="text" name="osoba" size="50" maxlength="50" value="<?php echo $_POST["osoba"];?>"><br>
	<span class="levySl">Kontaktn� adresa:</span> <input type="text" name="kadresa" size="50" maxlength="50" value="<?php echo $_POST["kadresa"];?>"><br>
	<span class="levySl">Telefon:</span> <input class="cislo" type="text" name="telefon" size="14" maxlength="14" value="<?php echo $_POST["telefon"];?>"> e-mail: <input type="text" name="email" size="30" maxlength="30" value="<?php echo $_POST["email"];?>"> <br><br>
	<span class="levySl">&nbsp;</span> <input type="submit" name="akce" value="Ulo�it"><input type="submit" name="akce" value="Nov� partner"><input type="submit" name="akce" value="Smazat">
	<script>
		if (window.opener!=undefined) {
			document.writeln("<input type=\"submit\" name=\"akce\" value=\"P�evz�t do smlouvy\" onClick=\"preved()\">");
		}
	</script>
	</form>
</div>
