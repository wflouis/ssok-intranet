<?php
require 'base-write.php';

$id = $_GET['id'];

$stmt = mysqli_prepare($link, "
delete from partneri where id_partnera = ?
");
$stmt->bind_param('i', $id);

if($stmt->execute()) http_response_code(200);
else http_response_code(500);