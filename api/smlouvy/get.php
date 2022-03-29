<?php
require 'base-read.php';

$search = '%' . $_GET['search'] . '%';

$typ = empty($_GET['typ']) ? '%' : $_GET['typ'];
$stredisko = empty($_GET['stredisko']) ? '%' : $_GET['stredisko'];

$order = empty($_GET['order']) ? 'cisloSmlouvy' : $_GET['order'];
$orderDirection = empty($_GET['order-direction']) ? 'asc' : $_GET['order-direction'];

$stmt = mysqli_prepare($link, "
select smlouvy.id_smlouvy as id, smlouvy.cisloSmlouvy, smlouvy.datumUzavreni, smlouvy.predmet, smlouvy.cena, smlouvy.velikost
from smlouvy
join smlouvystr on smlouvystr.id_smlouvy = smlouvy.id_smlouvy
join seznam_str on seznam_str.id_str = smlouvystr.id_strediska
where
    cisloSmlouvy like ? and
    typSmlouvy like ? and
    seznam_str.nazev like ?
order by $order $orderDirection
limit 200
");
echo mysqli_error($link);
$stmt->bind_param('sss', $search, $typ, $stredisko);
$stmt->execute();
$result = $stmt->get_result();
echo $stmt->error;

echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));