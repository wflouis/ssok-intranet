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
datumDo
) 
values (?,?,?,?,?,?,?,?,?)
");
$stmt->bind_param('sssssssss', $obj['cisloSmlouvy'],$obj['typSmlouvy'],$obj['datumUzavreni'],$obj['predmet'],$obj['cena'],$obj['velikost'],$obj['rodneCislo'],$obj['datumOd'],$obj['datumDo']);
echo $stmt->error;

if($stmt->execute()) {
    $id = $stmt->insert_id;
    postStrediska($id, $obj['strediska']);
    postPartneri($id, $obj['partneri']);

    http_response_code(200);
}
else {
    http_response_code(500);
}