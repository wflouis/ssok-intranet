<?php
require 'base-write.php';

$stmt = mysqli_prepare($link, "
insert into seznam (jmeno, funkce, telefon, mobil, email, stredisko, internet)
values (?,?,?,?,?,?,?)
");
$stmt->bind_param('sssssss', $obj['jmeno'], $obj['funkce'], $obj['telefon'], $obj['mobil'], $obj['email'], $obj['stredisko'], $obj['internet']);
echo $stmt->error;

print_r($obj);

if($stmt->execute()) {
    $id = $stmt->insert_id;
    echo "{\"id\":$id}";
    
    postOpravneni($id, $obj['opravneni']);

    http_response_code(200);
}
else {
    echo $stmt->error;
    http_response_code(500);
}