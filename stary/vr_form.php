<div class=levySl>Zadávající:</div>
<div class=txPole><?php echo $_SESSION["zadavajici"];?></div>
<div class=levySl>Jednací èíslo:</div>
<div><input type="text" name="cj" size="20" maxlength="25" value="<?php echo $_POST["cj"];?>"></div>
<div class=levySl>Datum vystavení:</div>
<div><input class=cislo type="text" name="datum" size="10" maxlength="10" value="<?php echo $_POST["datum"];?>"> Stáhnout dne: <input class=cislo type="text" name="datumStazeni" size="10" maxlength="10" value="<?php echo $_POST["datumStazeni"];?>"></div>
<div class=levySl>Pøedmìt:</div>
<div><textarea cols="50" rows="4" name="predmet"><?php echo $_POST["predmet"];?></textarea></div>
<div class=levySl>Termín pro podání nabídek:</div>
<div><input class=cislo type="text" name="termin1" size="10" maxlength="10" value="<?php echo $_POST["termin1"];?>"> do <input class=cislo type="text" name="termin2" size="5" maxlength="5" value="<?php echo $_POST["termin2"];?>"></div>
<div class=levySl>Poznámka:</div>
<div><textarea cols="50" rows="4" name="poznamka"><?php echo $_POST["poznamka"];?></textarea></div>
<div class=levySl>Pøíloha:</div><div><?php echo (!empty($_POST["priloha"]))?$_POST["priloha"]."&nbsp;&nbsp;":"";?><input type="file" name="soubor" size="40"></div><br>
<div class=flVlevo><input class=tlacitko type="submit" name="akce" value="Uloit"> <input type="submit" name="akce" value="Novı záznam"></div>
