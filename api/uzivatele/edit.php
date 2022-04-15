<?php
require 'base-write.php';

$stmt = mysqli_prepare($link, "
update seznam set
jmeno = ?,
funkce = ?,
telefon = ?,
email = ?,
stredisko = ?,
internet = ?

where id_jmeno = ?
");
$stmt->bind_param('ssssssi', $obj['jmeno'], $obj['funkce'], $obj['telefon'], $obj['email'], $obj['stredisko'], $obj['internet'], $obj['id']);

postOpravneni($obj['id'], $obj['opravneni']);

if($stmt->execute()) http_response_code(200);
else {
    echo $stmt->error;
    http_response_code(500);
}