<?php
require 'base-write.php';

$obj = json_decode(file_get_contents("php://input"), true);

$stmt = mysqli_prepare($link, "
update seznam_str set
zkratka = ?,
nazev = ?

where id_str = ?
");
$stmt->bind_param('ssi', $obj['zkratka'], $obj['nazev'], $obj['id']);

postOstatni($obj['zkratka'], $obj['ostatni']);

if($stmt->execute()) http_response_code(200);
else {
    echo $stmt->error;
    http_response_code(500);
}