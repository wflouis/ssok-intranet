<?php
require 'base-read.php';

$stredisko = isset($_GET['stredisko']) ? $_GET['stredisko'] : '';
$search = isset($_GET['search']) ? ('%' . $_GET['search'] . '%') : '%';
$order = empty($_GET['order']) ? 'jmeno' : $_GET['order'];
$orderDirection = empty($_GET['order-direction']) ? 'desc' : $_GET['order-direction'];

$stmt = mysqli_prepare($link, "
select id_jmeno as id, jmeno, funkce, email, telefon, mobil, stredisko, internet,
  (select group_concat(opravneni_moduly.zkratka separator '')
  from opravneni
  join opravneni_moduly on opravneni_moduly.id_modulu = opravneni.id_modulu
  where opravneni.id_jmeno = seznam.id_jmeno) as opravneni
from seznam
where stredisko like ?
  and jmeno COLLATE utf8_unicode_ci like ?
order by $order $orderDirection
");
echo mysqli_error($link);
$stmt->bind_param('ss', $stredisko, $search);
$stmt->execute();
$result = $stmt->get_result();
echo $stmt->error;

$users = mysqli_fetch_all($result, MYSQLI_ASSOC);

echo json_encode($users);