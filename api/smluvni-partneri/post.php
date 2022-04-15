<?php
require 'base-write.php';



$stmt = mysqli_prepare($link, "
insert into partneri (nazev, ico, mesto, ulice, psc, osoba, kadresa, telefon, email) 
values (?,?,?,?,?,?,?,?,?)
");
$stmt->bind_param('sssssssss', $obj['nazev'], $obj['ico'],$obj['mesto'],$obj['ulice'],$obj['psc'],$obj['osoba'],$obj['kadresa'],$obj['telefon'],$obj['email']);
echo $stmt->error;

if($stmt->execute()) {
    $id = $stmt->insert_id;
    echo "{\"id\":$id}";
    http_response_code(200);
}
else {
    http_response_code(500);
}