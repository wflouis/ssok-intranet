<?php
require 'base-write.php';



$stmt = mysqli_prepare($link, "
insert into seznam_str (zkratka,nazev)
values (?,?)
");
$stmt->bind_param('ss', $obj['zkratka'], $obj['nazev']);
echo $stmt->error;

if($stmt->execute()) {
    postOstatni($stmt->insert_id, $obj['ostatni']);

    http_response_code(200);
}
else {
    echo $stmt->error;
    http_response_code(500);
}