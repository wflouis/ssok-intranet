<?php
	$homePage = true;
	include 'over.php';
	include "hlava.php";
	include "nabidka.php"; 
?>

<div>
	<script src='js/seznam.js' defer></script>
	<script src='js/smlouvy.js' defer></script>

	<h2 class="obsah-title">Smlouvy</h2>
	<div class="obsah">
		<div class='flex'>
			<select id="select-typ">
				<option value="%">Vše</option>
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
			<select id="select-stredisko">
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
		<div class='flex'>
			<select id='select-rok'>
				<option value=''>Vše</option>
				<?php
					for($year = date('Y'); $year >= 2002; $year--){
						echo "<option value='$year'>$year</option>";
					}
				?>
			</select>
			<div class='gap'></div>
			<span class='flex flex-center-v'>platnost od: </span><input id='input-od' type='date'>
			<span class='flex flex-center-v'>  do: </span><input id='input-do' type='date'>
		</div>
		<table id='smlouvy' class="table">
			<thead>
				<tr>
					<td column="cisloSmlouvy">Číslo</td>
					<td column="popis">Typ</td>
					<!-- <td column="predmet">Předmět</td> -->
					<td column="datumUzavreni" title='Datum uzavření'>Datum uz.</td>
					<td column="cena">Cena</td>
					<td column="velikost">Velikost</td>
					<td column="strediska" nosort title='Střediska'>Střed.</td>
					<td column="partneri" nosort>Partneři</td>
					<td column="rodneCislo" title='Fyzická osoba'>Fyz. osoba</td>
					<td column="datumOd">Od</td>
					<td column="datumDo">Do</td>
					<td column="faktury" nosort>Faktury</td>
					<td column="prilohy" nosort>Přílohy</td>
					<!-- <td column="zaruky">Záruky</td> -->
					<td nosort>Akce</td>
				</tr>
			</thead>
			<tbody id="table-body">

			</tbody>
		</table>
	</div>
</div>
<?php include "pata.php"; ?>