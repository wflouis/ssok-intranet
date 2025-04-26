<?php
require dirname(__FILE__) . '/../base-write.php';

$idSmlouvy = $obj['id_smlouvy'];
$idZaruky = $obj['id_zaruky'];

$stmt = mysqli_prepare($link, "UPDATE zaruky set
predmetZaruky = ?,
datumZarukyOd = ?,
datumZarukyDo = ?

WHERE id_smlouvy = ?
    and id_zaruky = ?
");

$stmt->bind_param('sssii', $obj['predmetZaruky'], $obj['datumZarukyOd'], $obj['datumZarukyDo'], $idSmlouvy, $idZaruky);

if($stmt->execute()){
    http_response_code(200);
}
else{
    http_response_code(500);
    echo mysqli_error($link);
}