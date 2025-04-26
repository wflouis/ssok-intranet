<?php
	$homePage = true;
	include 'over.php';
	include "hlava.php";
	include "nabidka.php"; 
?>

<div>
	<script src='js/seznam.js' defer></script>
	<script src='js/smluvni-partneri.js' defer></script>

	<h2 class="obsah-title">Seznam smluvních partnerů</h2>
	<div class="obsah">
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