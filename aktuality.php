<?php
	$homePage = true;
	include 'over.php';
	include "hlava.php";
	include "nabidka.php";
?>

<div>
	<script>let writePermission = <?=(strpos($_SESSION['prava'], 'A') !== false ? 'true' : 'false')?></script>
	<script>let fromId = <?=$_SESSION['id_jmeno']?>;</script>
	<script src='js/seznam.js' defer></script>
	<script src='js/aktuality.js' defer></script>

	<h2 class="obsah-title">Aktuality</h2>
	<?=tableText()?>
	<div class="obsah">
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
