<?php
require 'base-write.php';

$stmt = mysqli_prepare($link, "
update partneri set
nazev = ?,
ico = ?,
mesto = ?,
ulice = ?,
psc = ?,
osoba = ?,
kadresa = ?,
telefon = ?,
email = ?

where id_partnera = ?
");
$stmt->bind_param('sssssssssi', $obj['nazev'], $obj['ico'],$obj['mesto'],$obj['ulice'],$obj['psc'],$obj['osoba'],$obj['kadresa'],$obj['telefon'],$obj['email'],$obj['id']);

if($stmt->execute()) http_response_code(200);
else {
    echo $stmt->error;
    http_response_code(500);
}