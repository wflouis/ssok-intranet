<?php
	$homePage = true;
	include 'over.php';
	include "hlava.php";
	include "nabidka.php";
?>

<div>
	<script>let writePermission = <?=(strpos($_SESSION['prava'], 'U') !== false ? 'true' : 'false')?></script>
	<script src='js/seznam.js' defer></script>
	<script src='js/uzivatele.js' defer></script>

	<h2 class="obsah-title">Seznam uživatelů</h2>
	<?=tableText()?>
	<div class="obsah">
		<select id="select">
		<?php
			$result = mysqli_query($link, 'select zkratka, nazev from seznam_str');
			while($radek = mysqli_fetch_assoc($result)){
		?>
			<option value="<?=$radek['zkratka']?>"><?=$radek['nazev']?></option>
		<?php
			}
		?>
		</select>
		<table class="table">
			<thead>
				<tr>
					<td column="jmeno">Jméno</td>
					<td column="funkce">Funkce</td>
					<td column="telefon">Telefon</td>
					<td column="mobil">Mobil</td>
					<td column="email">E-mail</td>
					<td column="stredisko">Středisko</td>
					<td column="internet">Internet</td>
					<td column="opravneni">Oprávnění</td>
					<td nosort>Akce</td>
				</tr>
			</thead>
			<tbody id="table-body">

			</tbody>
		</table>
	</div>
</div>
<?php include "pata.php"; ?>
