<?php
require 'base-write.php';



$stmt = mysqli_prepare($link, "
insert into seznam_str (zkratka,nazev,poradi)
values (?,?,?)
");
$stmt->bind_param('ssi', $obj['zkratka'], $obj['nazev'], $obj['poradi']);
echo $stmt->error;

if($stmt->execute()) {
    $id = $stmt->insert_id;
    echo "{\"id\":$id}";

    postOstatni($id, $obj['ostatni']);

    http_response_code(200);
}
else {
    echo $stmt->error;
    http_response_code(500);
}