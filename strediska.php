<?php
	$homePage = true;
	include 'over.php';
	include "hlava.php";
	include "nabidka.php"; 
?>

<div>
	<script src='js/seznam.js' defer></script>
	<script src='js/strediska.js' defer></script>

	<h2 class="obsah-title">Seznam středisek</h2>
	<div class="obsah">
		<table id='strediska' class="table">
			<thead>
				<tr>
					<td column="zkratka">Zkratka</td>
					<td column="nazev">Název</td>
					<td column="poradi">Pořadí</td>
					<td column="ostatni" nosort>Ostatní</td>
					<td column="ostatni" nosort>Akce</td>
				</tr>
			</thead>
			<tbody id="table-body">

			</tbody>
		</table>
	</div>
</div>
<?php include "pata.php"; ?>