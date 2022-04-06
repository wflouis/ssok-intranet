<?php
require 'base-read.php';

$obj = json_decode(file_get_contents("php://input"), true);

$order = $_GET['order'];
$orderDirection = $_GET['order-direction'];

$where = isset($_GET['from-id']) ? "where zpravy.id_jmeno = {$_GET['from-id']}" : '';

$result = mysqli_query($link, "SELECT
seznam.jmeno, zpravy.id, zpravy.datum, zpravy.text
FROM zpravy
JOIN seznam ON seznam.id_jmeno = zpravy.id_jmeno
$where
ORDER BY $order $orderDirection
");
echo mysqli_error($link);

if($result) {
    http_response_code(200);
    echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));
}
else {
    http_response_code(500);
}