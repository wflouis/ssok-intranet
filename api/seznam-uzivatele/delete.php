<?php
require 'base-write.php';

$userId = $_GET['id'];

$stmt = mysqli_prepare($link, "
delete from seznam where id_jmeno = ?
");
$stmt->bind_param('i', $userId);

if($stmt->execute()) http_response_code(200);
else http_response_code(500);