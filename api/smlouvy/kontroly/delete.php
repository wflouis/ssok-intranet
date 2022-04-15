<?php
require dirname(__FILE__) . '/../base-write.php';

$idSmlouvy = $obj['id_smlouvy'];
$idZaruky = $obj['id_zaruky'];
$idKontroly = $obj['id_kontroly'];

$result = mysqli_query($link, "DELETE
    FROM kontroly
    WHERE id_smlouvy = $idSmlouvy
        and id_zaruky = $idZaruky
        and id_kontroly = $idKontroly
");

if($result){
    http_response_code(200);
}
else{
    http_response_code(500);
    echo mysqli_error($link);
}