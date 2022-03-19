<?php
	include "over.php"; 
	include "hlava.php"; 
	$_GET["modul"] = 1;
	include "nabidka.php"; 
?>
	    <div class="col-sm-9">
			<h2>Nástěnka</h2>
<?php
  			$result = mysqli_query($link,"SELECT S.jmeno, Z.* FROM zpravy Z JOIN seznam S ON S.id_jmeno=Z.id_jmeno ORDER BY datum desc ");
			while ($radek = mysqli_fetch_assoc($result)) { ?>
				<div class="zprava">
					<div class="zahlaviZpravy"><?php echo date("d.m.Y",strtotime($radek["datum"]))." v ".date("H:i",strtotime($radek["datum"]))." - autor: ".$radek["jmeno"]; ?></div>
					<div class="textZpravy"><?php echo $radek["text"]; ?></div>
				</div>
<?php		} ?>
	    </div>
<?php	include "pata.php"; ?>
