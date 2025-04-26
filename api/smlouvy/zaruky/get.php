<?php
require dirname(__FILE__) . '/../base-read.php';

$idSmlouvy = $_GET['id-smlouvy'];

if($idSmlouvy == '') die(json_encode([]));

$order = $_GET['order'];
$orderDirection = $_GET['order-direction'];

$result = mysqli_query($link, "SELECT
    id_zaruky, predmetZaruky, datumZarukyOd, datumZarukyDo
    from zaruky
    where id_smlouvy = {$idSmlouvy}
    
    order by $order $orderDirection
");

echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));