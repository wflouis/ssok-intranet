<?php
require 'base-read.php';

$search = '%' . $_GET['search'] . '%';
$order = empty($_GET['order']) ? 'nazev' : $_GET['order'];
$orderDirection = empty($_GET['order-direction']) ? 'desc' : $_GET['order-direction'];

$stmt = mysqli_prepare($link, "
select id_partnera as id, nazev, ico, mesto, ulice, psc, osoba, kadresa, telefon, email
from partneri
where lower(nazev) like lower(?)
order by $order $orderDirection
limit 200
");
echo mysqli_error($link);
$stmt->bind_param('s', $search);
$stmt->execute();
$result = $stmt->get_result();
echo $stmt->error;

echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));