	<?php 
	if (isset($_POST["zaznam"]) and $_POST["zaznam"]>0)
		$result = mysqli_query($spojeni, "select * from DochZaznamy where id='".$_POST["zaznam"]."'");
		if ($radek=mysqli_fetch_assoc($result)) { 
			$_POST["den"]=date("d",strtotime($radek["cas"]));
			$_POST["cas"]=date("G:i",strtotime($radek["cas"]));
			$_POST["idTypuZaznamu"]=$radek["idTypuZaznamu"];
		}
	?>
	<h2>Zmìna záznamù</h2>
	<form action="administrace.php" method="post" name="zaznamy" id="zaznamy">
		<input type="hidden" name="zalozka" value="2">
		<input type="hidden" name="zaznam" id="zaznam" value="<?php echo $_POST["zaznam"]; ?>">
			Den: <input type="text" class="cislo" name="den" id="den" size="2" maxlength="2" value="<?php echo $_POST["den"]; ?>">
			èas: <input type="text" name="cas" id="cas" size="5" maxlength="5" value="<?php echo $_POST["cas"]; ?>"> 
			Pøerušení: <select name="idTypuZaznamu">
			<?php  $result = mysqli_query($spojeni, "select * from DochTypyZaznamu"); 
				while ($radek=mysqli_fetch_assoc($result)) 
					echo "<option value=\"".$radek["id"]."\" ".(($radek["id"]==$_POST["idTypuZaznamu"])?"SELECTED":"").">".$radek["Popis"]."</option>";
			?>
			</select><br><br>
		<input type="submit" name="tlacitko" value="Ulož"><input type="button" name="tlacitko" value="Nový" onClick="novy()"><input type="submit" name="tlacitko" value="Smaž" onclick="return window.confirm('Opravdu chete smazat pøerušení?');">
	</form>
