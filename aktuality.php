<?php
	$homePage = true;
	include 'over.php';
	include "hlava.php";
	include "nabidka.php";
?>

<div>
	<script>let fromId = <?=$_SESSION['id_jmeno']?>;</script>
	<script src='js/seznam.js' defer></script>
	<script src='js/aktuality.js' defer></script>

	<h2>Aktuality</h2>
	<div class="obsah">
		<!-- <textarea id='txt-text' class='txt'></textarea> -->
		<button id='new-button' class='btn'>Nová zpráva</button>
		<div class="gap"></div>
		<table class="table">
			<thead>
				<tr>
					<td column="datum" class='sort-asc'>Datum</td>
					<td column="text">Text</td>
					<td column="mail" nosort>Mail</td>
					<td nosort>Akce</td>
				</tr>
			</thead>
			<tbody id="table-body">

			</tbody>
		</table>
	</div>
</div>
<?php include "pata.php"; ?>