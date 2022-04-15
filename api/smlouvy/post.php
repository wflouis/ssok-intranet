<?php
require 'base-write.php';



$stmt = mysqli_prepare($link, "
insert into smlouvy (
cisloSmlouvy,
typSmlouvy,
datumUzavreni,
predmet,
cena,
velikost,
rodneCislo,
datumOd,
datumDo,
faktura,
uhrazeno,
zadal
) 
values (?,?,?,?,?,?,?,?,?,?)
");
echo mysqli_error($link);
$stmt->bind_param('sssssssssssi', $obj['cisloSmlouvy'],$obj['typSmlouvy'],$obj['datumUzavreni'],$obj['predmet'],$obj['cena'],$obj['velikost'],$obj['rodneCislo'],$obj['datumOd'],$obj['datumDo'],$obj['faktura'],$obj['uhrazeno'],$_SESSION['id_jmeno']);

if($stmt->execute()) {
    $id = $stmt->insert_id;
    echo "{\"id\":$id}";

    postStrediska($id, $obj['strediska']);
    postPartneri($id, $obj['partneri']);
    postFaktury($obj['id'], $obj['faktury']);
    deletePrilohy($id, $obj['deletePrilohy']);
    postPrilohy($id, $obj['postPrilohy']);

    http_response_code(200);
}
else {
    echo $stmt->error;
    http_response_code(500);
}