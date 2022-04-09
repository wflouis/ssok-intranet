<?php
require 'base-write.php';

$user = json_decode(file_get_contents("php://input"), true);

$stmt = mysqli_prepare($link, "
insert into seznam (jmeno, funkce, telefon, email, stredisko, internet)
values (?,?,?,?,?,?)
");
$stmt->bind_param('ssssss', $user['jmeno'], $user['funkce'], $user['telefon'], $user['email'], $user['stredisko'], $user['internet']);
echo $stmt->error;

print_r($user);

if($stmt->execute()) {
    postOpravneni($stmt->insert_id, $user['opravneni']);

    http_response_code(200);
}
else {
    echo $stmt->error;
    http_response_code(500);
}