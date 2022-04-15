<?php
require dirname(__FILE__) . '/../base-write.php';

$idSmlouvy = $obj['id_smlouvy'];
$idZaruky = $obj['id_zaruky'];
$idKontroly = $obj['id_kontroly'];

$stmt = mysqli_prepare($link, "UPDATE kontroly set
datumKontroly = ?,
vysledekKontroly = ?,
zavady = ?,
datumOdstraneni = ?

WHERE id_smlouvy = ?
    and id_zaruky = ?
    and id_kontroly = ?
");

$stmt->bind_param('ssssiii', $obj['datumKontroly'], $obj['vysledekKontroly'], $obj['zavady'], $obj['datumOdstraneni'], $idSmlouvy, $idZaruky, $idKontroly);

if($stmt->execute()){
    http_response_code(200);
}
else{
    http_response_code(500);
    echo mysqli_error($link);
}