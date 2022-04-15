<?php
require 'base-write.php';

$stmt = mysqli_prepare($link, "
update smlouvy set
cisloSmlouvy = ?,
typSmlouvy = ?,
datumUzavreni = ?,
predmet = ?,
cena = ?,
velikost = ?,
rodneCislo = ?,
datumOd = ?,
datumDo = ?,
faktura = ?,
uhrazeno = ?

where id_smlouvy = ?
");
$stmt->bind_param('sssssssssssi', $obj['cisloSmlouvy'],$obj['typSmlouvy'],$obj['datumUzavreni'],$obj['predmet'],$obj['cena'],$obj['velikost'],$obj['rodneCislo'],$obj['datumOd'],$obj['datumDo'],$obj['faktura'],$obj['uhrazeno'],$obj['id']);

if($stmt->execute()) {
    postStrediska($obj['id'], $obj['strediska']);
    postPartneri($obj['id'], $obj['partneri']);
    postFaktury($obj['id'], $obj['faktury']);
    deletePrilohy($obj['id'], $obj['deletePrilohy']);
    postPrilohy($obj['id'], $obj['postPrilohy']);

    http_response_code(200);
}
else {
    echo $stmt->error;
    http_response_code(500);
}