<?php
require dirname(__FILE__) . '/../base-write.php';

$idSmlouvy = $obj['id_smlouvy'];

$idZaruky = mysqli_fetch_assoc(mysqli_query($link, "SELECT coalesce(max(id_zaruky), 0) as id_zaruky
from zaruky
where id_smlouvy = $idSmlouvy"))['id_zaruky'] + 1;

$stmt = mysqli_prepare($link, "INSERT INTO zaruky
(id_smlouvy, id_zaruky, predmetZaruky, datumZarukyOd, datumZarukyDo, zadal)
values (?,?,?,?,?,?)
");

$stmt->bind_param('iisssi', $idSmlouvy, $idZaruky, $obj['predmetZaruky'], $obj['datumZarukyOd'], $obj['datumZarukyDo'], $_SESSION['id_jmeno']);

if($stmt->execute()){
    $id = $stmt->insert_id;
    echo "{\"id_zaruky\":$id}";
    http_response_code(200);
}
else{
    http_response_code(500);
    echo mysqli_error($link);
}