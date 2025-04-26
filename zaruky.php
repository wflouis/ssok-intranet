<?php
	$homePage = true;
	include 'over.php';
	include "hlava.php";
	include "nabidka.php"; 
?>

<div>
	<script src='js/seznam.js' defer></script>
	<script src='js/zaruky.js' defer></script>

	<h2 class="obsah-title">Seznam záruk</h2>
	<div class="obsah">
		<div class='flex'>
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
			<div class='gap'></div>
			<select id="select-zadavatel">
				<option value="">Všichni</option>
				<?php
					$result = mysqli_query($link, 'select distinct zaruky.zadal as id, seznam.jmeno from zaruky join seznam on seznam.id_jmeno = zaruky.zadal');
					while($radek = mysqli_fetch_assoc($result)){
				?>
					<option value="<?=$radek['id']?>" <?php
						if(isset($_GET['alert'])){
							if($radek['id'] == $_SESSION['id_jmeno']) echo 'selected';
						}
					?>><?=$radek['jmeno']?></option>
				<?php
					}
				?>
			</select>
		</div>
		<div class='flex'>
			<span class='flex flex-center-v'>od: </span><input id='input-od' type='date' <?php
				if(isset($_GET['alert'])){
					echo 'value=' . date('Y-m-d');
				}
			?>>
			<div class='gap'></div>
			<span class='flex flex-center-v'>  do: </span><input id='input-do' type='date' <?php
				
			?>>
		</div>
		
		<table id='zaruky' class="table">
			<thead>
				<tr>
					<td column="cisloSmlouvy">Číslo smlouvy</td>
					<td column="predmetZaruky">Předmět</td>
					<td column="datumZarukyOd">Od</td>
					<td column="datumZarukyDo">Do</td>
					<td column="zadavatel">Zadavatel</td>
					<td column="strediska">Střediska</td>
					<td column="kontroly">Kontroly</td>
					<!-- <td nosort>Akce</td> -->
				</tr>
			</thead>
			<tbody id="table-body">

			</tbody>
		</table>
	</div>
</div>
<?php include "pata.php"; ?>