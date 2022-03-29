<?php
	$homePage = true;
	include 'over.php';
	include "hlava.php";
	include "nabidka.php"; 
?>

<div>
	<script src='js/seznam.js' defer></script>
	<script src='js/smlouvy.js' defer></script>

	<h2>Seznam uživatelů</h2>
	<div class="obsah">
		<div class='flex'>
			<select id="select-typ" class="select-menu">
			<?php
				$result = mysqli_query($link, 'select id_typuSmlouvy as id, popis from typysmluv');
				while($radek = mysqli_fetch_assoc($result)){
			?>
				<option value="<?=$radek['id']?>"><?=$radek['popis']?></option>
			<?php
				}
			?>
			</select>
			<div class='gap'></div>
			<select id="select-stredisko" class="select-menu">
			<?php
				$result = mysqli_query($link, 'select zkratka, nazev from seznam_str');
				while($radek = mysqli_fetch_assoc($result)){
			?>
				<option value="<?=$radek['zkratka']?>"><?=$radek['nazev']?></option>
			<?php
				}
			?>
			</select>
		</div>
		<div class='flex '>
			<select id='select-rok' class='select-menu'>
				<option value='%'>Vše</option>
				<?php
					for($year = date('Y'); $year >= 2002; $year--){
						echo "<option value='$year'>$year</option>";
					}
				?>
			</select>
			<div class='gap'></div>
			<span class='flex flex-center-v'>platnost od: </span><input id='platnost-od' type='date'>
			<span class='flex flex-center-v'>  do: </span><input id='platnost-do' type='date'>
		</div>
		<div class='flex flex-center-v'>
			<input id="search" class="txt txt-width">
			<div class='gap'></div>
			<a id='new-button'>Nová smlouva</a>
		</div>
		<div class="gap"></div>
		<table class="table">
			<thead>
				<tr>
					<td column="cisloSmlouvy">Číslo</td>
					<td column="predmet">Předmět</td>
					<td column="datumUzavreni">Datum uzavření</td>
					<td column="cena">Cena</td>
					<td column="velikost">Velikost</td>
					<td nosort>Akce</td>
				</tr>
			</thead>
			<tbody id="table-body">

			</tbody>
		</table>
	</div>
</div>
<?php include "pata.php"; ?>