<?php
require 'base-write.php';

$user = json_decode(file_get_contents("php://input"), true);

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
$stmt->bind_param('ssssssi', $user['jmeno'], $user['funkce'], $user['telefon'], $user['email'], $user['stredisko'], $user['internet'], $user['id_jmeno']);

postOpravneni($user['id'], $user['opravneni']);

if($stmt->execute()) http_response_code(200);
else {
    echo $stmt->error;
    http_response_code(500);
}