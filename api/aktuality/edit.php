<?php
require 'base-write.php';



$stmt = mysqli_prepare($link, "UPDATE zpravy
set text = ?
where id = ? and id_jmeno = ?");
$stmt->bind_param('sii', $obj['text'], $obj['id'], $_SESSION['id_jmeno']);
echo $stmt->error;

if($stmt->execute()) {
    sendEmails($obj);
    http_response_code(200);
}
else {
    http_response_code(500);
}