<?php
  header('Expires: ' . gmdate('D, d M Y H:i:s') . '  GMT');
  header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . '  GMT');
  header('Content-Type: text/xml; charset=utf-8');
?>
<?xml version="1.0" encoding="utf-8"?>
<?xml-stylesheet type="text/css" href="xml.css"?>
<seznam>
	<stredisko>
		<nazev>Stredisko udrzby Olomouc</nazev>
		<detail>
			<adresa><span class="tucne">Adresa:</span> Lipenska 753/120, 779 00 Olomouc</adresa>
			<telefon>Dispecer ZU - prima linka: 585 311 049</telefon>
			<telefon>Vratnice: 585 151 422</telefon>
			<email>Hlavni e-mail: olomouc@ssok.cz</email>
			<kontakty>
				<kontakt class="zahlavi">
					<jmeno>Jmeno</jmeno>
					<funkce>funkce</funkce>
					<telefon>telefon</telefon>
					<email>e-mail</email>
				</kontakt>
				<kontakt>
					<jmeno>Arnos Vaclav, Ing.</jmeno>
					<funkce>vedouci TSU</funkce>
					<telefon>585 151 414</telefon>
					<email>arnos@ssok.cz</email>
				</kontakt>
				<kontakt>
					<jmeno>Arnostova Vaclava, Ing.</jmeno>
					<funkce>ucetni</funkce>
					<telefon>585 151 414</telefon>
					<email>arnostova@ssok.cz</email>
				</kontakt>
			</kontakty>
		</detail>
	</stredisko>
</seznam>