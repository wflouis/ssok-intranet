<?php
	$homePage = true;
	
	include "over.php";
	include "hlava.php"; 
	$_GET["modul"] = 1;
	include "nabidka.php"; 

	include 'zprava.php';
?>
	<div>
		<h2>Nástěnka</h2>
		<?php
			$result = mysqli_query($link,"SELECT S.jmeno, Z.* FROM zpravy Z JOIN seznam S ON S.id_jmeno=Z.id_jmeno ORDER BY datum desc ");
			while ($radek = mysqli_fetch_assoc($result)) {
				echo getZprava($radek);
			}
		?>
	</div>
<?php include "pata.php"; ?>
