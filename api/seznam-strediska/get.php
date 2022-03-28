<?php
require 'base-read.php';

$search = '%' . $_GET['search'] . '%';
$order = empty($_GET['order']) ? 'nazev' : $_GET['order'];
$orderDirection = empty($_GET['order-direction']) ? 'desc' : $_GET['order-direction'];

$stmt = mysqli_prepare($link, "
select id_str as id, zkratka, nazev as name
from seznam_str
where lower(nazev) like lower(?)
order by $order $orderDirection
");
echo mysqli_error($link);
$stmt->bind_param('s', $search);
$stmt->execute();
$result = $stmt->get_result();
echo $stmt->error;

$rows = [];
while($row = mysqli_fetch_assoc($result)){
  $other = mysqli_fetch_all(mysqli_query($link, "select nadpis, text from strediska where stredisko = '{$row['zkratka']}'"), MYSQLI_ASSOC);

  $row['ostatni'] = $other;
  $rows[] = $row;
}
echo json_encode($rows);