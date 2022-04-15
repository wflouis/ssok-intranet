<?php
require 'base-read.php';

$search = '%' . $_GET['search'] . '%';

$stredisko = empty($_GET['stredisko']) ? '%' : $_GET['stredisko'];
$zadavatel = $_GET['zadavatel'];

$od = '0000-0-0';
$do = '9999-0-0';
if(!empty($_GET['od'])){
    $od = $_GET['od'];
}
if(!empty($_GET['do'])){
    $do = $_GET['do'];
}


$order = empty($_GET['order']) ? 'nazev' : $_GET['order'];
$orderDirection = empty($_GET['order-direction']) ? 'desc' : $_GET['order-direction'];

$stmt = mysqli_prepare($link, "SELECT zaruky.id_smlouvy, smlouvy.cisloSmlouvy, predmetZaruky, datumZarukyOd, datumZarukyDo, seznam.jmeno as zadavatel,
(
    select GROUP_CONCAT(seznam_str.zkratka SEPARATOR '\n')
    from smlouvystr
    join seznam_str on seznam_str.id_str = smlouvystr.id_strediska
    where smlouvystr.id_smlouvy = zaruky.id_smlouvy
) as strediska,
(
    select group_concat(concat(datumKontroly, ' ', vysledekKontroly, '\nodstranění: ', datumOdstraneni) separator '\n\n')
    from kontroly
    where id_smlouvy = zaruky.id_smlouvy and id_zaruky = zaruky.id_zaruky
) as kontroly

from zaruky
left join smlouvy on smlouvy.id_smlouvy = zaruky.id_smlouvy
left join seznam on seznam.id_jmeno = zaruky.zadal

where 
(smlouvy.cisloSmlouvy like ? or predmetZaruky collate utf8_general_ci like ? )
and EXISTS (
    select seznam_str.zkratka from smlouvystr
    join seznam_str on seznam_str.id_str = smlouvystr.id_strediska
    where
        smlouvystr.id_smlouvy = zaruky.id_smlouvy and
        seznam_str.zkratka like ?
)
and (seznam.id_jmeno = ? or ? = '')
and datumZarukyOd <= ?
and datumZarukyDo >= ?


order by $order $orderDirection
limit 50
");
echo mysqli_error($link);
$stmt->bind_param('sssssss', $search, $search, $stredisko, $zadavatel, $zadavatel, $do, $od);
$stmt->execute();
$result = $stmt->get_result();
echo $stmt->error;

echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));

// and datumZarukyOd >= ? and datumZarukyOd <= ?
// and datumZarukyDo <= ? and datumZarukyDo >= ?


// order by $order $orderDirection
// limit 50
// ");
// echo mysqli_error($link);
// $stmt->bind_param('sssssssss', $search, $search, $stredisko, $zadavatel, $zadavatel, $od, $do, $do, $od);