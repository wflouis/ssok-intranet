<?php
require dirname(__FILE__) . '/../base-write.php';

$idSmlouvy = $obj['id_smlouvy'];
$idZaruky = $obj['id_zaruky'];

$idKontroly = mysqli_fetch_assoc(mysqli_query($link, "SELECT coalesce(max(id_kontroly), 0) as id_kontroly
from kontroly
where id_smlouvy = $idSmlouvy
    and id_zaruky = $idZaruky"))['id_kontroly'] + 1;

$stmt = mysqli_prepare($link, "INSERT INTO kontroly
(id_smlouvy, id_zaruky, id_kontroly, datumKontroly, vysledekKontroly, zavady, datumOdstraneni)
values (?,?,?,?,?,?,?)
");

$stmt->bind_param('iiissss', $idSmlouvy, $idZaruky, $idKontroly, $obj['datumKontroly'], $obj['vysledekKontroly'], $obj['zavady'], $obj['datumOdstraneni']);

if($stmt->execute()){
    $id = $stmt->insert_id;
    echo "{\"id_kontroly\":$id}";
    http_response_code(200);
}
else{
    http_response_code(500);
    echo mysqli_error($link);
}