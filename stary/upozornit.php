<?php
	if (maPristup("Z",true)) {
		$result = mysqli_query($_SESSION["link"],"SELECT cisloSmlouvy, kdy, text FROM (SELECT smlouvy.id_smlouvy, cisloSmlouvy, datumZarukyDo as kdy, predmetZaruky as text, zaruky.zadal   
FROM zaruky join smlouvy on zaruky.id_smlouvy=smlouvy.id_smlouvy 
WHERE datumZarukyDo >='".date("Y-m-d")."' and datumZarukyDo <'".date("Y-m-d",strtotime("+2 month"))."') A 
WHERE id_smlouvy in (SELECT DISTINCT id_smlouvy FROM smlouvyStr JOIN 
(SELECT s.id_str FROM seznam_str s JOIN 
(SELECT id_str FROM seznam_str JOIN seznam on zkratka=stredisko WHERE id_jmeno='".$_SESSION["id_jmeno"]."') n ON s.id_str=n.id_str or nadrazene=n.id_str) 
str ON id_strediska=str.id_str) or A.zadal='".$_SESSION["id_jmeno"]."'
ORDER BY kdy");
		$text = ""; 
		while($radek = mysqli_fetch_assoc($result))
			$text .= DateEnCz($radek["kdy"]).": ".$radek["cisloSmlouvy"]." ".$radek["text"]."<br>";
		if (!empty($text)) {
			echo "<a id=\"vykricnik\" href=\"#\" onClick=\"getObj('upozornit').style.display='block';getObj('upozornit2').style.display='block'\">!</a>";
			echo "<div id=\"upozornit\"><u>Pøipomínky záruk:</u>";
			echo "<a id=\"konec\" href=\"#\" onClick=\"this.parentElement.style.display='none';getObj('upozornit2').style.display='none';\">X</a><br>";
			echo $text;
			echo "</div>";
		}
		$result = mysqli_query($_SESSION["link"],"SELECT cisloSmlouvy, kdy, text FROM (SELECT smlouvy.id_smlouvy, cisloSmlouvy, kdy, text FROM smlouvy WHERE upozornit='1' and kdy>'".date("Y-m-d",strtotime("-1 month"))."' and kdy<='".date("Y-m-d")."') A 
WHERE id_smlouvy in (SELECT DISTINCT id_smlouvy FROM smlouvyStr JOIN 
(SELECT s.id_str FROM seznam_str s JOIN 
(SELECT id_str FROM seznam_str JOIN seznam on zkratka=stredisko WHERE id_jmeno='".$_SESSION["id_jmeno"]."') n ON s.id_str=n.id_str or nadrazene=n.id_str) 
str ON id_strediska=str.id_str)
ORDER BY kdy");
		$text = ""; 
		while($radek = mysqli_fetch_assoc($result))
			$text .= DateEnCz($radek["kdy"]).": ".$radek["cisloSmlouvy"]." ".$radek["text"]."<br>";
		if (!empty($text)) {
			echo "<div id=\"upozornit2\"><u>Pøipomínky faktur:</u>";
			echo "<a id=\"konec\" href=\"#\" onClick=\"this.parentElement.style.display='none';getObj('upozornit').style.display='none'\">X</a><br>";
			echo $text;
			echo "</div>";
		} else {
//			echo "<div id=\"upozornit2\"></div>";
		}
	}
	
/*
	SELECT cisloSmlouvy, kdy, text FROM (SELECT smlouvy.id_smlouvy, cisloSmlouvy, datumZarukyDo as kdy, predmetZaruky as text  
FROM zaruky join smlouvy on zaruky.id_smlouvy=smlouvy.id_smlouvy 
WHERE datumZarukyDo >='".date("Y-m-d")."' and datumZarukyDo <'".date("Y-m-d",strtotime("+2 month"))."' union 
SELECT smlouvy.id_smlouvy, cisloSmlouvy, kdy, text FROM smlouvy WHERE upozornit='1' and kdy>'".date("Y-m-d",strtotime("-1 month"))."' and kdy<='".date("Y-m-d")."') A 
WHERE id_smlouvy in (SELECT DISTINCT id_smlouvy FROM smlouvyStr JOIN 
(SELECT s.id_str FROM seznam_str s JOIN 
(SELECT id_str FROM seznam_str JOIN seznam on zkratka=stredisko WHERE id_jmeno='".$_SESSION["id_jmeno"]."') n ON s.id_str=n.id_str or nadrazene=n.id_str) 
str ON id_strediska=str.id_str)
ORDER BY kdy
*/
?>

