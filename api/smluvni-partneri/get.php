<?php
require 'base-read.php';

$search = '%' . $_GET['search'] . '%';
$order = empty($_GET['order']) ? 'nazev' : $_GET['order'];
$orderDirection = empty($_GET['order-direction']) ? 'desc' : $_GET['order-direction'];

$stmt = mysqli_prepare($link, "
select id_partnera as id, nazev, ico, mesto, ulice, psc, osoba, kadresa, telefon, email
from partneri
where nazev COLLATE utf8_unicode_ci like ? or lower(ico) like lower(?) or mesto COLLATE utf8_unicode_ci like ? or ulice COLLATE utf8_unicode_ci like ? 
order by $order $orderDirection
limit 200
");
echo mysqli_error($link);
$stmt->bind_param('ssss', $search, $search, $search, $search);
$stmt->execute();
$result = $stmt->get_result();
echo $stmt->error;

echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));