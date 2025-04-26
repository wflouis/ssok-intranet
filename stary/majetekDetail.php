<div class="ouska">
	<ul>
		<li <?php echo (!$detail)?"class=\"aktivni\"":"";?> id="ousko1">Seznam</li>
		<li <?php echo ($detail)?"class=\"aktivni\"":"";?>id="ousko2">Zmìny</li>
	</ul>
</div>
<div id="okraj"></div>
<div id="oknoDetail">
	<form action="majetek.php" method="post" name="formDetail" id="formDetail">
	
	<input type="hidden" name="raditPodle" value="<?php echo $_POST["raditPodle"];?>">
	<input type="hidden" name="zmeny" value="1">
	<input type="hidden" name="najit" value="<?php echo $_POST["najit"];?>">
	<input type="hidden" name="id_majetku" value="<?php echo $_POST["id_majetku"];?>">

	<span class="levySl">Typ majetku:</span> 
	<select name="typMajetku" onChange="">
	<?php $result = mysql_query("SELECT * FROM typyMajetku WHERE 1=1");
		while ($radek = mysql_fetch_assoc($result))
			echo "<option value=\"".$radek["id_typuMajetku"]."\" ".(($radek["id_typuMajetku"]==$_POST["typMajetku"])?" selected":"").">".$radek["popis"]."</option>";
	?>
	</select>
	støedisko: <select name="stredisko" onChange="">
	<?php $result = mysql_query("SELECT * FROM seznam_str WHERE 1=1 ORDER BY poradi");
		while ($radek = mysql_fetch_assoc($result))
			echo "<option value=\"".$radek["id_str"]."\"".(($radek["id_str"]==$_POST["stredisko"])?" selected":"").">".$radek["nazev"]."</option>";
	?>
	</select><br>
	<span class="levySl">Inventární èíslo:</span> <input type="text" name="invCislo" size="15" maxlength="15" value="<?php echo $_POST["invCislo"];?>"> <br>
	<?php if ($result = mysql_query("SELECT d.*,p.hodnota FROM definiceParametru d left join parametryMajetku p on (id_parametru=parametr  and majetek = '".$_POST["id_majetku"]."') WHERE d.typMajetku='".$_POST["typMajetku"]."'"))
			while ($radek = mysql_fetch_assoc($result)) {
				echo "<span class=\"levySl\">".$radek["popis"]."</span> <input type=\"text\" name=\"p".$radek["id_parametru"]."\" size=\"40\" maxlength=\"40\" value=\"".htmlspecialchars($radek["hodnota"])."\"> <br>\n";		
			} 
	?><br>
	<span class="levySl">&nbsp;</span> <input type="submit" name="akce" value="Uložit"><input type="submit" name="akce" value="Nový majetek"><input type="submit" name="akce" value="Smazat">
	</form>
</div>
