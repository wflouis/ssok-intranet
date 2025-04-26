<?php
require 'base-write.php';

$id = $_GET['id'];

$stmt = mysqli_prepare($link, "DELETE from zpravy where id = ? and id_jmeno = ?");
$stmt->bind_param('ii', $id, $_SESSION['id_jmeno']);
echo $stmt->error;

if($stmt->execute()) {
    http_response_code(200);
}
else {
    http_response_code(500);
}