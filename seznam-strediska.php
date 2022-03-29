<?php
	$homePage = true;
	include 'over.php';
	include "hlava.php";
	include "nabidka.php"; 
?>

<div>
	<script src='js/seznam.js' defer></script>
	<script src='js/seznam-strediska.js' defer></script>

	<h2>Seznam středisek</h2>
	<div class="obsah">
    <div class='flex flex-center-v'>
      <input id="search" class="txt txt-width">
      <!-- <button id='search-btn' class='btn btn-width'><i class='fa fa-search'></i></button> -->
      <div class='gap'></div>
      <a id='new-button'>Nové středisko</a>
    </div>

		<div class="gap"></div>
		<table id='strediska' class="table">
			<thead>
				<tr>
					<td column="zkratka">Zkratka</td>
					<td column="nazev">Název</td>
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