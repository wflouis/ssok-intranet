<?php
require dirname(__FILE__) . '/../base-read.php';

$idSmlouvy = $_GET['id-smlouvy'];
$idZaruky = $_GET['id-zaruky'];

$order = $_GET['order'];
$orderDirection = $_GET['order-direction'];

if($idSmlouvy == '') die(json_encode([]));

$result = mysqli_query($link, "SELECT
    id_smlouvy, id_zaruky, id_kontroly, datumKontroly, vysledekKontroly, zavady, datumOdstraneni
    from kontroly
    where id_smlouvy = {$idSmlouvy}
        and id_zaruky = {$idZaruky}
    order by $order $orderDirection
");

echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));