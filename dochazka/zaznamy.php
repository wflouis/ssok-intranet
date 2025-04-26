<div class="telo" id="o2">

	<br>

	<?php 

	if (isset($_SESSION["administrator"])) 

		 $odkaz = "administrace";

	else

	     $odkaz = "pichacky";



	echo "<a class=\"tlacitko\" href=\"$odkaz.php?zalozka=2&amp;smer=-1\">Pøedchozí</a>";

	echo "<span class=\"h3\">Období: ".$mesiceL[$mesic-1]." ".$rok."</span>";

	echo "<a class=\"tlacitko\" href=\"$odkaz.php?zalozka=2&amp;smer=1\">Následující</a><br><br><br>";



	if (isset($_SESSION["administrator"])) 

		include "zmenaZazn.php";

?>

	<table <?php echo (isset($_SESSION["administrator"]))?"class=\"seznamZam\"":"class=\"zaznamy\""; ?> align="center" cellpadding="0" cellspacing="0">

	<tr class="zahlavi"><td>Den</td><td>èas</td><td>typ</td><td>poznámka</td></tr>

	<?php

	$query="

	select D.id, D.cas, D.poznamka, T.popis from DochZaznamy D left join DochTypyZaznamu T on D.idTypuZaznamu=T.id where D.idZam='".$_SESSION["user"]."' and cas between '$datumOd' and '$datumDo' order by cas";

	$result = mysqli_query($spojeni, $query); 

	while ($radek=mysqli_fetch_assoc($result)) 

			echo "<tr id=\"z".$radek["id"]."\"><td>".date("d.m.",strtotime($radek["cas"]))."</td><td class=\"zarVpravo\">".date("G:i",strtotime($radek["cas"]))."</td><td>".$radek["popis"]."</td><td>".$radek["poznamka"]."</td></tr>";

	?>

	</tr>

	</table>

</div>

<div class="telo" id="o3"><br>

	<?php 

	if (!empty($zamestnanec["pracoviste"]))

		echo "<div>Pracovištì: ".$zamestnanec["pracoviste"]."</div>";

	echo "<h3>Období: ".$mesiceL[$mesic-1]." ".$rok."</h3>";

	echo "<h3>Os.èíslo: ".$zamestnanec["osCislo"]." - ".$zamestnanec["prijmeni"]." ".$zamestnanec["jmeno"]."</h3>";

	?>

	<table class="dochList" cellpadding="0" cellspacing="0" align="center">

	<tr class="zahlavi">

		<td colspan="2" class="zarVlevo">Den</td><td>Pøíchod</td><td>Odchod</td><td>Pøestávky</td><td>Odpracoval</td><td>Omluv.</td><td>So,Ne</td><td>Svátek</td><td>Dovolená</td><td>Nemoc</td><td>OÈR</td><td>IV</td><td class="zarVlevo">Pozn.</td>

	</tr>

	<?php

	$query="select * from DochParametry where 1=1";

	$result=mysqli_query($spojeni, $query);

	$parametry=mysqli_fetch_assoc($result);



	$query="select mesic, den from svatky where statni='1' or rok='".$rok."'";

	$result = mysqli_query($spojeni, $query); 

	while($radek=mysqli_fetch_assoc($result)) {

		$svatky[$radek["mesic"]][$radek["den"]]=1;

	}

	

	$soucetDny = 0;

	$pracDni = 0;

	$pracDniKeDni = 0;

	$pocetSvatku = 0;

	$dovolena = 0;

	$nemoc = 0; 

	$ocr = 0 ; 

	$nv = 0; 

	

	$query="

	select idTypuZaznamu from DochZaznamy where cas in (select max(cas) from DochZaznamy where idZam='".$_SESSION["user"]."' and cas between '$datumOdPrev' and '$datumDoPrev')";

	$result = mysqli_query($spojeni, $query); 



	if ($radek=mysqli_fetch_assoc($result)) 

		switch ($radek["idTypuZaznamu"]) {

		case "3":	$dovolena = 1; break;

		case "4":	$nemoc = 1; break;

		case "5":	$ocr = 1; break;

		case "6":	$nv = 1;

		}



	$query="

	select cas, poznamka, idTypuZaznamu as typ from DochZaznamy  

	where idZam='".$_SESSION["user"]."' and cas between '$datumOd' and '$datumDo' order by cas";

	$result = mysqli_query($spojeni, $query); 



	$soucetOdprac = 0;

	$soucetSone = 0;

	$soucetSvatek = 0;

	$soucetDovolena = 0;

	$soucetNemoc = 0;

	$soucetOCR = 0;

	$soucetNV = 0;

	$soucetOmluveno = 0;

	

	$radek=mysqli_fetch_assoc($result); 

	for($zpracDen=1;$zpracDen<=date("j",mktime(0, 0, 0, $mesic+1, 0, $rok));$zpracDen++) {

		$denVTydnu = $dnyZkr[date("w",mktime(0, 0, 0, $mesic, $zpracDen, $rok))];

		$vikend = ($denVTydnu=="So" or $denVTydnu=="Ne");

		$svatek = isset($svatky[$mesic][$zpracDen]);

		$casy = array();  

		$odpracovano = $castDovolene = $zacatek = $konec = $omluveno = 0;

		$dovolenaVMin = $zamestnanec["uvazek"]*60;

		$poznamka = ""; 

		echo "<tr ".(($vikend or $svatek)?" class=\"sone\"":"")."><td>".$zpracDen.".</td><td class=\"den\">".$denVTydnu."</td>";

		if (isset($radek["cas"]) and date("j",strtotime($radek["cas"]))==$zpracDen) {

			do {

				if ($radek["typ"]==1)

					if ($zacatek==0)

						$zacatek = $radek["cas"];

					else 
						if ($konec>0)
							$omluveno += ceil(max(strtotime($radek["cas"])-strtotime($konec),0)/60/$parametry["zaokrouhleni"])*$parametry["zaokrouhleni"];

				if ($radek["typ"]>1 and $zacatek>0)

					$konec = $radek["cas"];

				$casy[$radek["typ"]] = $radek["cas"];

				$poznamka .= $radek["poznamka"]." ";

			} while ($radek=mysqli_fetch_assoc($result) and date("j",strtotime($radek["cas"]))==$zpracDen);

			$posledni = max(array_values($casy)); 

			$typ = array_flip($casy); 

			//*** èasový záznam oøízne o sekundy, pøidá korekci a zaokrouhlí dle nastavení nahoru/dolù (pøíchod/odchod) 

			$prichod = $odchod = 0;

			if ($zacatek>0)
				// vše menší než 6h se zaokrouhlí na 6
				if (date("G",strtotime($zacatek))<6)
					$prichod = ceil(strtotime($zacatek)/3600)*3600;
				else
					$prichod = ceil((intval(strtotime($zacatek)/60)+$parametry["pridatNaZacatku"])/$parametry["zaokrouhleni"])*$parametry["zaokrouhleni"]*60;

			if ($konec>0)

				$odchod = floor((intval(strtotime($konec)/60)+$parametry["pridatNaKonci"])/$parametry["zaokrouhleni"])*$parametry["zaokrouhleni"]*60;

			//*** rozdíl se pøevede na minuty

			$rozdilCasu = max(($odchod-$prichod)/60,0); 

			$prestavka = ($rozdilCasu>=6*60)?30:0;
//			$prestavka = ($rozdilCasu-$omluveno>=6*60)?30:0;

			$odpracovano = $rozdilCasu-$prestavka; 

			/*** výpoèet pùldne dovolené **************/

			if (($dovolena or $typ[$posledni]==3) and $odpracovano>0 and $odpracovano<($dovolenaVMin*$parametry["generovatDovolenou"]/100) and !$vikend and !$svatek) {

				$castDovolene = 0.5;

			}

			/********/

			$soucetOdprac += $odpracovano;

			$soucetOmluveno += $omluveno;

			if ($odpracovano>0)

				$soucetDny++;

			if ($vikend)

				$soucetSone += $odpracovano;

			if ($svatek)

				$soucetSvatek += $odpracovano;

			$dovolena = $nemoc = $ocr = $nv = 0; 

			switch ($typ[$posledni]) {

			case "3":	$dovolena = 1; break;

			case "4":	$nemoc = 1; break;

			case "5":	$ocr = 1; break;

			case "6":	$nv = 1;

			}

			echo "<td>".(($prichod>0)?date("G:i",$prichod):"")."</td><td>".(($odchod>0)?date("G:i",$odchod):"")."</td><td>".formatCasu($prestavka)."</td><td>".formatCasu($odpracovano)."</td><td>".formatCasu($omluveno)."</td><td>".(($vikend)?formatCasu($odpracovano):"")."</td><td>".(($svatek)?formatCasu($odpracovano):"")."</td>";

		} else

			echo "<td colspan=\"7\"></td>"; 



		if ($svatek)

			$pocetSvatku++;

		else 

			if (!$vikend) {

				$pracDni++;

				if ($zpracDen<=$dnes)

					$pracDniKeDni++;

			}

		if (($odpracovano==0 or $castDovolene>0) and !$vikend and !$svatek and $zpracDen<=$dnes) {

			if ($castDovolene>0) {

				$dovolena = 1;

				$dovolenaVMin /= 2;

				$soucetDovolena -= .5;

			}

			switch (1) {

			case $dovolena: $soucetDovolena++; break;

			case $nemoc: $soucetNemoc++; break;

			case $ocr: $soucetOCR++; break;

			case $nv: $soucetNV++;

			}

			echo "<td>".(($dovolena)?formatCasu($dovolenaVMin):"")."</td><td>".(($nemoc)?formatCasu($zamestnanec["uvazek"]*60):"")."</td><td>".(($ocr)?formatCasu($zamestnanec["uvazek"]*60):"")."</td><td>".(($nv)?formatCasu($zamestnanec["uvazek"]*60):"")."</td><td>$poznamka</td></tr>";

		} else

			echo "<td></td><td></td><td></td><td></td><td>$poznamka</td></tr>";

	}

	echo "<tr class=\"zahlavi\"><td colspan=\"2\"></td><td>Norma</td><td>ke dni</td><td>celkem =</td><td>Odpracoval</td><td></td><td>So,Ne</td><td>Svátek</td><td>Dovolená</td><td>Nemoc</td><td>OÈR</td><td>IV</td><td></td></tr>";

	echo "<tr class=\"soucet\"><td colspan=\"2\"></td><td>".formatCasu($pracDni*$zamestnanec["uvazek"]*60)."</td><td>".formatCasu($pracDniKeDni*$zamestnanec["uvazek"]*60)."</td><td>".formatCasu($soucetOdprac+($soucetDovolena+$soucetNemoc+$soucetOCR+$soucetNV)*$zamestnanec["uvazek"]*60)."</td><td>".formatCasu($soucetOdprac)."</td><td>".formatCasu($soucetOmluveno)."</td><td>".formatCasu($soucetSone)."</td><td>".formatCasu($soucetSvatek)."</td><td>".formatCasu($soucetDovolena*$zamestnanec["uvazek"]*60)."</td><td>".formatCasu($soucetNemoc*$zamestnanec["uvazek"]*60)."</td><td>".formatCasu($soucetOCR*$zamestnanec["uvazek"]*60)."</td><td>".formatCasu($soucetNV*$zamestnanec["uvazek"]*60)."</td><td></td></tr>";

	echo "<tr class=\"soucet\"><td colspan=\"2\"></td><td>$pracDni</td><td>$pracDniKeDni</td><td>".($soucetDny+$soucetDovolena+$soucetNemoc+$soucetOCR+$soucetNV)."</td><td>".$soucetDny."</td><td></td><td></td><td>".$pocetSvatku."</td><td>".$soucetDovolena."</td><td>".$soucetNemoc."</td><td>".$soucetOCR."</td><td>".$soucetNV."</td><td></td></tr>";

	

	function formatCasu($hodnota) {

		return (($hodnota>0)?intval($hodnota/60).":".sprintf("%02d",$hodnota%60):"");

	}

	?>

	</table>

</div>

