<?php
require 'base-write.php';

$stmt = mysqli_prepare($link, "
update seznam_str set
zkratka = ?,
nazev = ?,
poradi = ?

where id_str = ?
");
$stmt->bind_param('ssii', $obj['zkratka'], $obj['nazev'], $obj['poradi'], $obj['id']);

postOstatni($obj['id'], $obj['ostatni']);

if($stmt->execute()) http_response_code(200);
else {
    echo $stmt->error;
    http_response_code(500);
}