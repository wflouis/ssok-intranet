<?php
	$homePage = true;
	include 'over.php';
	include "hlava.php";
	include "nabidka.php"; 
?>

<div>
	<script src='js/seznam.js' defer></script>
	<script src='js/smluvni-partneri.js' defer></script>

	<h2>Seznam uživatelů</h2>
	<div class="obsah">
	<div class='flex flex-center-v'>
		<input id="search" class="txt txt-width">
		<div class='gap'></div>
		<a id='new-button'>Nový partner</a>
	</div>
		<div class="gap"></div>
		<table class="table">
			<thead>
				<tr>
					<td column="nazev">Název</td>
					<td column="ico">IČO</td>
					<td column="mesto">Město</td>
					<td column="ulice">Ulice</td>
					<td column="psc">PSČ</td>
					<td column="osoba">Kont. osoba</td>
					<td column="kadresa">Kont. adresa</td>
					<td column="telefon">Telefon</td>
					<td column="email">E-mail</td>
					<td nosort>Akce</td>
				</tr>
			</thead>
			<tbody id="table-body">

			</tbody>
		</table>
	</div>
</div>
<?php include "pata.php"; ?>