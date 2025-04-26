<?php
header("Content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"windows-1250\"?>\n"; 
include "funkce/funkce.php"; 
include "funkce/databaze.php"; 

$result = mysql_query("SELECT text FROM cpvKody WHERE kod='".$_GET["kod"]."' LIMIT 1");
if ($radek=mysql_fetch_assoc($result)) {
	echo "<cpv>\n";
		echo "\t<text>".$radek["text"]."</text>\n";
	echo "</cpv>\n";
} 
?> 