<div class="ouska">
	<ul>
		<li <?php echo ($_SESSION["okno"]=='1')?"class=\"aktivni\"":"";?> id="ousko1">Smlouvy</li>
		<li <?php echo ($_SESSION["okno"]=='2')?"class=\"aktivni\"":"";?> id="ousko2">Detail</li>
		<li <?php echo ($_SESSION["okno"]=='3')?"class=\"aktivni\"":"";?> id="ousko3">Záruky</li>
		<li <?php echo ($_SESSION["okno"]=='3')?"class=\"aktivni\"":"";?> id="ousko4">Chyby exportu</li>
	</ul>
</div>
<div id="okraj"></div>
<div id="oknoDetail">
	<form action="smlouvy2.php" method="post" enctype="multipart/form-data" name="formDetail" id="formDetail" onsubmit="return kontrola()">
	
	<input type="hidden" name="raditPodle" value="<?php echo $_POST["raditPodle"];?>">
	<input type="hidden" name="stredisko" value="<?php echo $_POST["stredisko"];?>">
	<input type="hidden" name="raditZaruky" value="<?php echo $_POST["raditZaruky"];?>">
	<input type="hidden" name="zmeny" value="<?php echo $_POST["zmeny"];?>">
	<input type="hidden" name="najit" value="<?php echo (isset($_POST["najit"])?$_POST["najit"]:"");?>">
	<input type="hidden" name="rok" value="<?php echo (isset($_POST["rok"])?$_POST["rok"]:"");?>">
	<input type="hidden" name="id_smlouvy" value="<?php echo (isset($_POST["id_smlouvy"])?$_POST["id_smlouvy"]:"");?>">
	<input type="hidden" name="vazba" value="<?php echo (isset($_POST["vazba"])?$_POST["vazba"]:"");?>">
	<input type="hidden" name="nazevPartnera" value="<?php echo (isset($_POST["nazevPartnera"])?$_POST["nazevPartnera"]:"");?>">
	<input type="hidden" name="souvisejici" value="<?php echo (isset($_POST["souvisejici"])?$_POST["souvisejici"]:"");?>">
	<input type="hidden" name="okno" value="2">
	<input type="hidden" name="zrusPrilohu">
	<input type="hidden" name="zrusPartnera">
	<input type="hidden" name="zverejneno" value="<?php echo (isset($_POST["zverejneno"])?$_POST["zverejneno"]:"");?>">
	<input type="hidden" name="idSmlouvyPO" value="<?php echo (isset($_POST["idSmlouvyPO"])?$_POST["idSmlouvyPO"]:"");?>">
	<input type="hidden" name="rs_id" value="<?php echo (isset($_POST["rs_id"])?$_POST["rs_id"]:"");?>">
	<input type="hidden" name="rs_id_verze" value="<?php echo (isset($_POST["rs_id_verze"])?$_POST["rs_id_verze"]:"");?>">
	<input type="hidden" name="rs_link" value="<?php echo (isset($_POST["rs_link"])?$_POST["rs_link"]:"");?>">
	<input type="hidden" name="datumUcinnosti" value="<?php echo (isset($_POST["datumUcinnosti"])?$_POST["datumUcinnosti"]:"");?>">
	
	<?php	if ($zmeny==" disabled")
		echo "<input type=\"hidden\" name=\"cisloSmlouvy\" value=\"".(isset($_POST["cisloSmlouvy"])?$_POST["cisloSmlouvy"]:"")."\">";
	?>

	<div class="sirka125">
		<span class="levySl">Typ smlouvy:</span> 
		<select name="typSmlouvy" onChange="" <?php echo $zmeny; ?>>
		<?php $result = mysqli_query($_SESSION["link"],"SELECT * FROM typySmluv WHERE 1=1");
			while ($radek = mysqli_fetch_assoc($result)) 
				echo "<option value=\"".$radek["id_typuSmlouvy"]."\" ".((isset($_POST["typSmlouvy"]) && $radek["id_typuSmlouvy"]==$_POST["typSmlouvy"])?" selected":"").">".$radek["popis"]."</option>";
		?>
		</select> Èíslo smlouvy:</span> <input type="text" name="cisloSmlouvy" size="20" maxlength="20" value="<?php echo (isset($_POST["cisloSmlouvy"])?$_POST["cisloSmlouvy"]:"");?>" <?php echo $zmeny; ?>><br>
		<span class="levySl">Datum uzavøení:</span> <input class="cislo" type="text" name="datumUzavreni" size="10" maxlength="10" value="<?php echo (isset($_POST["datumUzavreni"])?$_POST["datumUzavreni"]:"");?>" <?php echo $zmeny; ?>> cena: <input type="text" name="cena" size="30" maxlength="30" value="<?php echo (isset($_POST["cena"])?$_POST["cena"]:"");?>" <?php echo $zmeny; ?>> <br>
		<span class="levySl">Smluvní strany:</span> <input class="cislo" type="text" name="ico" size="10" maxlength="8" onChange="this.form.submit();" <?php echo $zmeny; ?>> <input type="button" name="partneri" value="partneøi ..." onclick="window.open('partneri.php','Adresáø');" <?php echo $zmeny; ?>>
		<?php
			$pocet = 0;
			if (isset($_SESSION["smluvniPartneri"])) {
				foreach($_SESSION["smluvniPartneri"] as $key => $value) {
					if (!$value["smazat"]) {
						$pocet++;
						echo "<br><span class=\"levySl\"></span>$pocet. ".$value["nazev"].((empty($zmeny))?"  &nbsp;&nbsp;<img src=\"img/delete.png\" alt=\"smazat partnea\" width=\"10\" height=\"10\" border=\"0\" onClick=\"smazPartnera('$key');\">":"");
					}
				}
			}
		?><br>
		<span class="levySl">Fyzická osoba:</span> <input type="text" name="rodneCislo" size="88" maxlength="100" value="<?php echo (isset($_POST["rodneCislo"])?$_POST["rodneCislo"]:"");?>" <?php echo $zmeny; ?>><br>
		<span class="levySl">Pøedmìt smlouvy:</span> <textarea cols="67" rows="3" name="predmet" <?php echo $zmeny; ?>><?php echo (isset($_POST["predmet"])?$_POST["predmet"]:"");?></textarea><br>
		<span class="levySl">Doba trvání:</span> <input type="text" name="datumTxt" size="30" maxlength="30" value="<?php echo (isset($_POST["datumTxt"])?$_POST["datumTxt"]:"");?>" <?php echo $zmeny; ?>> od: <input type="text" class="cislo" name="datumOd" size="10" maxlength="10" value="<?php echo (isset($_POST["datumOd"])?$_POST["datumOd"]:"");?>" <?php echo $zmeny; ?>> do: <input type="text" class="cislo" name="datumDo" size="10" maxlength="10" value="<?php echo (isset($_POST["datumDo"])?$_POST["datumDo"]:"");?>" <?php echo $zmeny; ?>><br>
		<span class="levySl">Pøipomenout:</span> <input type="checkbox" name="upozornit" <?php echo ((isset($_POST["upozornit"]) && $_POST["upozornit"])?"checked":""); ?> <?php echo $zmeny; ?>> kdy: <input type="text" name="kdy" size="10" maxlength="10" value="<?php echo (isset($_POST["kdy"])?$_POST["kdy"]:"");?>" <?php echo $zmeny; ?>> text: <input type="text" name="text" size="50" maxlength="50" value="<?php echo (isset($_POST["text"])?$_POST["text"]:"");?>" <?php echo $zmeny; ?>><br>
		<?php  $klic = -1;
		if (isset($_POST["faktury"])) {
			foreach($_POST["faktury"] as $klic => $hodnota) {
				if (!empty($_POST["uhrady"][$klic]))
				   echo "<span class=\"levySl\">".(($klic<=0)?"Faktury:":"")."</span> uhrazeno dne: <input class=\"cislo\" type=\"text\" name=\"uhrady[$klic]\" size=\"10\" maxlength=\"10\" value=\"".$_POST["uhrady"][$klic]."\" $fakturace> uhr.èástka v Kè: <input type=\"text\" name=\"faktury[$klic]\" size=\"15\" maxlength=\"15\" value=\"".$_POST["faktury"][$klic]."\" $fakturace> <br>";
			}
		} ?>
		<span class="levySl"><?php $klic++; echo (($klic<=0)?"Faktury:":""); ?></span> uhrazeno dne: <input class="cislo" type="text" name="uhrady[<?php echo $klic; ?>]" size="10" maxlength="10" value="" <?php echo $fakturace; ?>> uhr.èástka v Kè: <input type="text" name="faktury[<?php echo $klic; ?>]" size="15" maxlength="15" value="" <?php echo $fakturace; ?>> <br>

		<span class="levySl">Pøílohy:</span> <input type="file" name="soubor" size="40" onChange="this.form.submit()" <?php echo $zmeny; ?>> <span class="popisek">(max.19MB)</span>
		<?php
			$pocet = 0;
			if (isset($_SESSION["prilohy"])) {
				foreach($_SESSION["prilohy"] as $key => $value) {
					if (!$value["smazat"]) {
						$pocet++;
						echo "<br><span class=\"levySl\"></span><a href=\"#\" id=\"z".$value["id_smlouvy"]."-".$value["cislo"]."\" class=\"zobrazit\">$pocet. <img src=\"img/".strtolower(substr(strrchr($value["name"],"."),1)).".gif\" border=\"0\"> ".$value["size"]." kB - ".$value["name"]."</a>".((empty($zmeny))?" &nbsp;&nbsp;<img src=\"img/delete.png\" alt=\"smazat pøílohu\" width=\"10\" height=\"10\" border=\"0\" onClick=\"smazPrilohu('$key');\">":"");
					}
				}
			}
		?>
		
		<div class="portalPO">
			<span class="levySl">Typ smlouvy PO:</span> <select name="typSmlouvyPO" onChange="this.form.submit()" <?php echo $zmeny; ?>>
			<?php $result = mysqli_query($_SESSION["link"],"SHOW COLUMNS FROM smlouvy LIKE 'typSmlouvyPO'");
				$radek = mysqli_fetch_assoc($result);
				$volbyNabidky = preg_split("/set\('|','|'\)/",$radek["Type"]); 
				echo "<option value=\"0\" ".((empty($_POST["typSmlouvyPO"]))?" selected":"")."></option>";
				foreach ($volbyNabidky as $hodnota) 
					if (!empty($hodnota))
						echo "<option value=\"".$hodnota."\" ".((isset($_POST["typSmlouvyPO"]) && $hodnota==$_POST["typSmlouvyPO"])?" selected":"").">".$hodnota."</option>";
			?></select> 
			<?php if (isset($_POST["typSmlouvyPO"]) && $_POST["typSmlouvyPO"]<>"dary") { ?>Stav smlouvy: <select name="stavPO" onChange="" <?php echo $zmeny; ?>>
			<?php $result = mysqli_query($_SESSION["link"],"select id_udaje, hodnota from typyUdajuSmluv where typSmlouvyPO='".$_POST["typSmlouvyPO"]."' and typ_udaje='stav'");
				while ($radek = mysqli_fetch_assoc($result)) 
					echo "<option value=\"".$radek["id_udaje"]."\" ".(($radek["id_udaje"]==$_POST["stavPO"])?" selected":"").">".$radek["hodnota"]."</option>";
			?></select> 
			<?php } ?>Koncept: <input type="checkbox" name="koncept" <?php echo ((isset($_POST["koncept"]) && $_POST["koncept"])?"checked":""); ?> <?php echo $zmeny; ?>> Èíslo na portále PO: <span style="color:#66ffff;"><?php echo (isset($_POST["idSmlouvyPO"])?$_POST["idSmlouvyPO"]:""); ?></span><br>
			<span class="levySl">Typ ceny:</span> <select name="typCeny" onChange="" <?php echo $zmeny; ?>>
				<option value="cena pevná" <?php echo ((isset($_POST["typCeny"]) && $_POST["typCeny"]=="cena pevná")?" selected":""); ?>>cena pevná</option>
				<option value="cena volná" <?php echo ((isset($_POST["typCeny"]) && $_POST["typCeny"]=="cena volná")?" selected":""); ?>>cena volná</option>
				<option value="bez finanèního plnìní" <?php echo ((isset($_POST["typCeny"]) && $_POST["typCeny"]=="bez finanèního plnìní")?" selected":""); ?>>bez finanèního plnìní</option>
			</select> 
			<?php if (isset($_POST["typSmlouvyPO"]) && $_POST["typSmlouvyPO"]=="dílèí") { ?>K rámcové smlouvì: <select name="ramcovaPO" onChange="" <?php echo $zmeny; ?>>
			<?php 
				if (isset($_SESSION["ramcove"])) {
					foreach ($_SESSION["ramcove"] as $smlouva)
						echo "<option value=\"".$smlouva->id_smlouvy."\" ".(($smlouva->id_smlouvy==$_POST["ramcova"])?" selected":"").">".$smlouva->cislo." - ".$smlouva->nazev."</option>";
				} else {
					$client = new SoapClient('https://verejnyportalpo.kr-olomoucky.cz/ws_sml/server.php?wsdl', array('encoding'=>'windows-1250', "trace" => 1, "exceptions" => 1));
					$portal=$client->get_ramcove($parametry);
					$_SESSION["ramcove"] = $portal->list;
					foreach ($portal->list as $smlouva)
						echo "<option value=\"".$smlouva->id_smlouvy."\" ".(($smlouva->id_smlouvy==$_POST["ramcova"])?" selected":"").">".$smlouva->cislo." - ".$smlouva->nazev."</option>";
				}
			?></select> 
			<?php } else { ?>Druh smlouvy: <select name="druhPO" onChange="" <?php echo $zmeny; ?>>
			<?php $result = mysqli_query($_SESSION["link"],"select id_udaje, hodnota from typyUdajuSmluv where typSmlouvyPO='".(isset($_POST["typSmlouvyPO"])?$_POST["typSmlouvyPO"]:"")."' and typ_udaje='druh'");
				while ($radek = mysqli_fetch_assoc($result)) 
					echo "<option value=\"".$radek["id_udaje"]."\" ".(($radek["id_udaje"]==$_POST["druhPO"])?" selected":"").">".$radek["hodnota"]."</option>";
			?></select>
			<?php } ?><br>
			<span class="levySl">Podtyp smlouvy:</span> <select name="typPO" onChange="" <?php echo $zmeny; ?>>
			<?php $result = mysqli_query($_SESSION["link"],"select id_udaje, hodnota from typyUdajuSmluv where typSmlouvyPO='".(isset($_POST["typSmlouvyPO"])?$_POST["typSmlouvyPO"]:"")."' and typ_udaje='typ'");
				while ($radek = mysqli_fetch_assoc($result)) 
					echo "<option value=\"".$radek["id_udaje"]."\" ".(($radek["id_udaje"]==$_POST["typPO"])?" selected":"").">".$radek["hodnota"]."</option>";
			?></select> 
			<?php if (isset($_POST["typSmlouvyPO"]) && $_POST["typSmlouvyPO"]=="nájemní") { ?>Pozice: <select name="pozicePO" onChange="" <?php echo $zmeny; ?>>
			<?php $result = mysqli_query($_SESSION["link"],"select id_udaje, hodnota from typyUdajuSmluv where typSmlouvyPO='".$_POST["typSmlouvyPO"]."' and typ_udaje='pozice'");
				while ($radek = mysqli_fetch_assoc($result)) 
					echo "<option value=\"".$radek["id_udaje"]."\" ".(($radek["id_udaje"]==$_POST["pozicePO"])?" selected":"").">".$radek["hodnota"]."</option>";
			?></select>
			<?php } ?><br>
			<span class="levySl">Zveøejnit:</span> <input type="checkbox" name="zverejnit" <?php echo ((isset($_POST["zverejnit"]) && $_POST["zverejnit"])?"checked":""); ?> <?php echo $zmeny; ?>> Zveøejnìno: <span style="color:#66ffff;"><?php echo ((isset($_POST["zverejneno"]) && $_POST["zverejneno"])?"ano":"ne"); ?></span> Možnost vypovìdìt do: <input class="cislo" type="text" name="datumVypovezeni" size="10" maxlength="10" value="<?php echo (isset($_POST["datumVypovezeni"])?$_POST["datumVypovezeni"]:"");?>" <?php echo $zmeny; ?>> <br>
			<span class="levySl">ID registru smluv:</span> <span style="color:#66ffff;"><?php echo (isset($_POST["rs_id"])?$_POST["rs_id"]:""); ?></span> ID verze: <span style="color:#66ffff;"><?php echo (isset($_POST["rs_id_verze"])?$_POST["rs_id_verze"]:""); ?></span> Datum úèinnosti: <span style="color:#66ffff;"><?php echo (isset($_POST["datumUcinnosti"])?$_POST["datumUcinnosti"]:""); ?></span> <br>
			<span class="levySl">Odkaz na smlouvu:</span> <span style="color:#66ffff;"><a target="_blank" href="<?php echo (isset($_POST["rs_link"])?$_POST["rs_link"]:""); ?>"><?php echo (isset($_POST["rs_link"])?$_POST["rs_link"]:""); ?></a></span> <br>
		</div>

		<br><br>
		<span class="levySl">&nbsp;</span> <input type="submit" name="akce" value="Uložit" onClick="<?php echo ((isset($_POST["idSmlouvyPO"]) && $_POST["idSmlouvyPO"]>0)?"alert('Tato smlouva byla již odeslána na portál PO! Proveïte pøípadné zmìny také na portále!');":""); ?>" <?php echo (($zmeny=="" or $zaruky=="" or $fakturace=="")?"":" disabled"); ?>><input type="submit" name="akce" value="Nová smlouva"><input type="submit" name="akce" value="Smazat" onClick="return confirm('Opravdu chcete smazat tuto smlouvu?')" <?php echo $zmeny; ?>> Souvisí s: <input type="submit" name="smlouvy" value="smlouvy ..." onclick="window.open('smlouvyVyber.php','Adresáø'); return false" <?php echo $zmeny; ?>> <input type="submit" name="akce" value="zrušit vazbu" <?php echo $zmeny; ?>>
		<span class="levySl">&nbsp;</span>
	</div>
	<div>
		Støedisko:<br>	
		<select name="strediska[]" size="17" multiple <?php echo $zmeny; ?>>
		<?php $result=mysqli_query($_SESSION["link"],"SELECT * FROM seznam_str WHERE poradi>0 order by poradi");
			while ($radek = mysqli_fetch_assoc($result)) {
				echo "<option value=\"".$radek["id_str"]."\" ".((!isset($_POST["strediska"]) || array_search($radek["id_str"],$_POST["strediska"])===false)?"":" selected").">".$radek["nazev"]."</option>\n";
			} 
		?>
		</select>
	</div>
	<span class="levySl">Související smlouvy:</span><span id="textVazba"></span><table>
	<?php
		if (!empty($_POST["vazba"]) or !empty($_POST["souvisejici"])) {
			if (!empty($_POST["vazba"]))
				$result = mysqli_query($_SESSION["link"],"select cisloSmlouvy, datumUzavreni, predmet, id_smlouvy, (SELECT nazev  FROM `smlouvyPrilohy` WHERE `id_smlouvy` = smlouvy.id_smlouvy order by cislo limit 1) as soubor, (SELECT velikost  FROM `smlouvyPrilohy` WHERE `id_smlouvy` = smlouvy.id_smlouvy order by cislo limit 1) as velikost, (SELECT cislo  FROM `smlouvyPrilohy` WHERE `id_smlouvy` = smlouvy.id_smlouvy order by cislo limit 1) as cislo from smlouvy where vazba = '".$_POST["vazba"]."' and id_smlouvy<>'".$_POST["id_smlouvy"]."'");
			else
				$result = mysqli_query($_SESSION["link"],"select * from smlouvy where id_smlouvy='".$_POST["souvisejici"]."'");
			$i = 0;
			while ($radek = mysqli_fetch_assoc($result)) {
				echo "<tr ".(($i%2==0)?"class=\"suda\"":"")."><td>".$radek["cisloSmlouvy"]."</td><td>".DateEnCz($radek["datumUzavreni"])."</td> <td>".$radek["predmet"]."</td><td><a href=\"#\" id=\"z".$radek["id_smlouvy"]."-".$radek["cislo"]."\" class=\"zobrazit\">".((!empty($radek["soubor"]))?"<img src=\"img/".strtolower(substr(strchr($radek["soubor"],"."),1,3)).".gif\" border=\"0\"> ".$radek["velikost"]." kB":"")."</a></td>\n";
				$i++;
			}
		} else
			echo "<tr><td>-</td></tr>";
	?></table><br>

	<?php 
	for($index=1;isset($_POST["predmetZaruky_$index"]) or $index==1 or (isset($_POST["akce"]) and $_POST["akce"]=='Nová záruka');$index++) {
		if (isset($_POST["predmetZaruky_$index"])) {
			$dniDoKonce = ceil((strtotime($_POST["datumZarukyDo_$index"])-time())/86400);
		} else {
			$dniDoKonce = 100;
		}
		echo "<div class=\"zaruka\">";
		echo "<span class=\"levySl\">Pøedmìt záruky:</span> <input type=\"text\" name=\"predmetZaruky_$index\" size=\"100\" maxlength=\"200\" value=\"".((isset($_POST["predmetZaruky_$index"]))?$_POST["predmetZaruky_$index"]:"")."\" $zaruky><br>\n";
		echo "<span class=\"levySl\">Poèátek záruky:</span> <input type=\"text\" class=\"cislo\" name=\"datumZarukyOd_$index\" size=\"10\" maxlength=\"10\" value=\"".((isset($_POST["datumZarukyOd_$index"]))?$_POST["datumZarukyOd_$index"]:"")."\" $zaruky> Konec záruky: <input type=\"text\" class=\"cislo\" name=\"datumZarukyDo_$index\" size=\"10\" maxlength=\"10\" value=\"".((isset($_POST["datumZarukyDo_$index"]))?$_POST["datumZarukyDo_$index"]:"")."\" $zaruky> <span class=\"konci\">".(($dniDoKonce<31 and $dniDoKonce>-1)?"< $dniDoKonce dní!":"")."</span><br><br>\n";
		echo "<span class=\"levySl\">&nbsp;</span> <input type=\"submit\" name=\"akce\" value=\"Uložit záruku\" $zaruky><input type=\"submit\" name=\"akce\" value=\"Nová záruka\" $zaruky></div>\n";
		for($indexK=1;isset($_POST["datumKontroly_$index"."_$indexK"]) or $indexK==1 or isset($_POST["akce_$index"]);$indexK++) {
			echo "<span class=\"levySl\">Výsledek kontroly:</span> <textarea cols=\"75\" rows=\"3\" name=\"vysledekKontroly_$index"."_$indexK\" $zaruky>".((isset($_POST["vysledekKontroly_$index"."_$indexK"]))?$_POST["vysledekKontroly_$index"."_$indexK"]:"")."</textarea><br>\n";
			echo "<span class=\"levySl\">Ze dne:</span> <input type=\"text\" class=\"cislo\" name=\"datumKontroly_$index"."_$indexK\" size=\"10\" maxlength=\"10\" value=\"".((isset($_POST["datumKontroly_$index"."_$indexK"]))?$_POST["datumKontroly_$index"."_$indexK"]:"")."\" $zaruky> Zjištìny závady: <input type=\"checkbox\" name=\"zavady_$index"."_$indexK\"".((isset($_POST["zavady_$index"."_$indexK"]) && $_POST["zavady_$index"."_$indexK"])?"checked":"")." $zaruky> odstranìny dne: <input type=\"text\" class=\"cislo\" name=\"datumOdstraneni_$index"."_$indexK\" size=\"10\" maxlength=\"10\" value=\"".((isset($_POST["datumOdstraneni_$index"."_$indexK"]))?$_POST["datumOdstraneni_$index"."_$indexK"]:"")."\" $zaruky> <br><br>\n";
			echo "<span class=\"levySl\">&nbsp;</span> <input type=\"submit\" name=\"akce\" value=\"Uložit záruku\" $zaruky><input type=\"submit\" name=\"akce_$index\" value=\"Další kontrola\" $zaruky><br>\n";
			if (!isset($_POST["vysledekKontroly_$index"."_$indexK"]))
				break;
		}
		if (!isset($_POST["predmetZaruky_$index"]))
			break;
	}
	?>		
	</form>
</div>
<div id="oknoZaruky">
	<form action="smlouvy2.php" method="post" name="formZaruky" id="formZaruky">
	
	<input type="hidden" name="raditZaruky" value="<?php echo (isset($_POST["raditZaruky"])?$_POST["raditZaruky"]:"");?>">
	<input type="hidden" name="raditPodle" value="<?php echo (isset($_POST["raditPodle"])?$_POST["raditPodle"]:"");?>">
	<input type="hidden" name="id_smlouvy" value="<?php echo (isset($_POST["id_smlouvy"])?$_POST["id_smlouvy"]:"");?>">
	<input type="hidden" name="rok" value="<?php echo (isset($_POST["rok"])?$_POST["rok"]:"");?>">
	<input type="hidden" name="okno" value="3">
	<input type="hidden" name="akce" value="Detail">
	<br>
	<span class="levySl">støedisko:</span> <select name="stredisko" onChange="">
		<option value="%">všechna støediska</option>
	<?php $result = mysqli_query($_SESSION["link"],"SELECT * FROM seznam_str WHERE 1=1");
		while ($radek = mysqli_fetch_assoc($result))
			echo "<option value=\"".$radek["id_str"]."\"".(($radek["id_str"]==$_POST["stredisko"])?" selected":"").">".$radek["nazev"]."</option>";
	?>
	</select>
	&nbsp;&nbsp;Konec záruky od &nbsp;<input type="text" class="cislo" name="zarukyOd" size="10" maxlength="10" value="<?php echo (isset($_POST["zarukyOd"])?$_POST["zarukyOd"]:"");?>"> do <input type="text" class="cislo" name="zarukyDo" size="10" maxlength="10" value="<?php echo (isset($_POST["zarukyDo"])?$_POST["zarukyDo"]:"");?>">
	<input type="checkbox" name="poZaruce" <?php echo (isset($_POST["poZaruce"]))?"checked":"";?>> zobrazit i po záruce <br><br>
	<span class="levySl">zadavatel:</span> <select name="jmeno" onChange="">
		<option value="%">od všech zadavatelù</option>
	<?php $result = mysqli_query($_SESSION["link"],"SELECT distinct id_jmeno, jmeno FROM `seznam` join zaruky on seznam.id_jmeno=zaruky.zadal ORDER by jmeno");
		while ($radek = mysqli_fetch_assoc($result))
			echo "<option value=\"".$radek["id_jmeno"]."\"".((isset($_POST["jmeno"]) && $radek["id_jmeno"]==$_POST["jmeno"])?" selected":"").">".$radek["jmeno"]."</option>";
	?>
	</select>
	<input type="submit" name="zobrazit" value="Zobrazit">&nbsp;&nbsp;<input type="checkbox" name="nevyresene" <?php echo (isset($_POST["nevyresene"]))?"checked":"";?>> pouze nedoøešené závady 
	<br><br>
	</form>
	<table id="zaruky" cellpadding="3" cellspacing="0">
	<thead class=HlTab><td onClick="seradZaruky('datumZarukyDo')">Konec záruky 
	<?php 
		if ($_POST["raditZaruky"]=="datumZarukyDo")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
	    echo "</td><td onClick=\"seradZaruky('cisloSmlouvy')\">Èíslo mlouvy ";
		if ($_POST["raditZaruky"]=="cisloSmlouvy")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
	    echo "</td><td colspan=\"2\" onClick=\"seradZaruky('predmetZaruky')\">Pøedmìt záruky ";
		if ($_POST["raditZaruky"]=="predmetZaruky")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
		echo "</td></thead>";
		$Sql  = "SELECT zaruky.*, cisloSmlouvy, datumKontroly, zavady, datumOdstraneni, vysledekKontroly FROM (zaruky join smlouvy on zaruky.id_smlouvy=smlouvy.id_smlouvy) left join kontroly on (zaruky.id_smlouvy=kontroly.id_smlouvy and zaruky.id_zaruky=kontroly.id_zaruky) ";
		$Sql .= "WHERE 1=1 ";
		if (!isset($_POST["poZaruce"])) {
			$Sql .= "and datumZarukyDo >= '".date("Y-m-d")."' ";
		} 
		if (isset($_POST["nevyresene"])) {
			$Sql .= "and zavady = 1 and datumOdstraneni='000-00-00' ";
		} 
		if (!empty($_POST["zarukyOd"])) {
			$Sql .= "and datumZarukyDo >= '".DateCzEn($_POST["zarukyOd"])."' ";
		} 
		if (!empty($_POST["zarukyDo"])) {
			$Sql .= "and datumZarukyDo <= '".DateCzEn($_POST["zarukyDo"])."' ";
		} 
		if (isset($_POST["stredisko"]) and $_POST["stredisko"]<>'%') {
			$Sql .= "and zaruky.id_smlouvy in (select id_smlouvy from smlouvyStr where id_strediska='".$_POST["stredisko"]."') ";
		} 
		if (isset($_POST["jmeno"]) and $_POST["jmeno"]<>'%') {
			$Sql .= "and zaruky.zadal = '".$_POST["jmeno"]."' ";
		} 
		$Sql .= "ORDER BY ".$_POST["raditZaruky"]."".$_SESSION["smer"];
		
		if (isset($_POST["zobrazit"])) {
			$seznam=mysqli_query($_SESSION["link"],$Sql); //echo $Sql; 
			$id_smlouvy = 0;
			$id_zaruky = 0;
			while($radek = @mysqli_fetch_assoc($seznam)) {
				if ($id_smlouvy <> $radek["id_smlouvy"] or $id_zaruky <> $radek["id_zaruky"]) {
					$dniDoKonce = ceil((strtotime($radek["datumZarukyDo"])-time())/86400);
					echo "<TR id=\"z".$radek["id_smlouvy"]."\"";
					echo " class=\"suda\">";
					echo "<td>".DateEnCz($radek["datumZarukyDo"])."</td><td>".$radek["cisloSmlouvy"]."</td><td>".$radek["predmetZaruky"]."</td><td><span class=\"konci\">".(($dniDoKonce<31 and $dniDoKonce>-1)?"< $dniDoKonce dní!":"")."</span></td>";
					echo "</tr>\n";
					$id_zaruky = $radek["id_zaruky"];
					$id_smlouvy = $radek["id_smlouvy"];
				}
				if (!is_null($radek["vysledekKontroly"])) {
					echo "<tr class=\"doplnek\"><td colspan=\"4\">Datum kontroly: <strong>".DateEnCz($radek["datumKontroly"])."</strong> Závady: ".(($radek["zavady"]==1)?" <span class=konci>ano</span>":"ne")." Odstranìno: ".DateEnCz($radek["datumOdstraneni"])."</td></tr>\n";
					echo "<tr class=\"doplnek\"><td colspan=\"4\">Výsledek kontroly: ".$radek["vysledekKontroly"]."</td></tr>\n";
				}
			} 
		}
	?>
	</table>
</div>
<div id="oknoChyby">
	<table id="seznamHlaseni" cellpadding="3" cellspacing="0">
	<thead class=HlTab><td onClick="serad('cisloSmlouvyPom')" width="80">Smlouva 
	<?php 
		if ($_POST["raditPodle"]=="cisloSmlouvy")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
	    echo "</td><td onClick=\"serad('datumUzavreni')\">uzavøena ";
		if ($_POST["raditPodle"]=="datumUzavreni")
			if ($_SESSION["smer"]==" asc")
		 		echo "<img src=\"img/up.gif\" alt=\"\" border=0>";
		 	else
		 		echo "<img src=\"img/down.gif\" alt=\"\" border=0>";
		echo "</td><td>pøedmìt smlouvy</td><td>cena</td><td>velikost</td></thead>";
		$Sql  = "SELECT smlouvy.*, smlouvyChyby.textChyby FROM smlouvy left join smlouvyChyby on smlouvy.id_smlouvy=smlouvyChyby.id_smlouvy where typSmlouvyPO>0 and idSmlouvyPO is null ";
		$Sql .= "ORDER BY ".$_POST["raditPodle"]."".$_SESSION["smer"];
		$seznam=mysqli_query($_SESSION["link"],$Sql); //echo $Sql; 
		while($radek = @mysqli_fetch_assoc($seznam)) {
			echo "<TR id=\"s".$radek["id_smlouvy"]."\"";
			echo " class=\"suda\">";
			echo "<td>".$radek["cisloSmlouvy"]."</td><td>".DateEnCz($radek["datumUzavreni"])."</td> <td>".$radek["predmet"]."</td><td>".$radek["cena"]."</td><td><a href=\"#\" id=\"z".$radek["id_smlouvy"]."\" class=\"zobrazit\">".((!empty($radek["soubor"]))?"<img src=\"img/".strtolower(substr(strchr($radek["soubor"],"."),1,3)).".gif\" border=\"0\"> ".$radek["velikost"]." kB":"")."</a></td>";
			echo "</tr>\n";
			echo "<tr><td></td><td colspan=\"4\" class=\"hlaseniPO\">Dùvod: ".(!empty($radek["textChyby"])?$radek["textChyby"]:(!empty($radek["koncept"])?"Smlouva je ve fázi konceptu":"Doklad èeká na odeslání"))."</td></tr>\n";
		} 
	?>
	</table>
</div>