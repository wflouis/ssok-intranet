<?php
require 'base-read.php';

$search = '%' . $_GET['search'] . '%';

$typ = empty($_GET['typ']) ? '%' : $_GET['typ'];
$stredisko = empty($_GET['stredisko']) ? '%' : $_GET['stredisko'];

$rokZacatek = '0000-0-0';
$rokKonec = date('Y') . '-12-31';
if(!empty($_GET['rok'])){
    $rok = $_GET['rok'];
    $rokZacatek = $rok . '-1-1';
    $rokKonec = $rok . '-12-31';
}

$platnostOd =  '0000-0-0';
$platnostDo = '9999-1-1';
if(!empty($_GET['platnost-od'])){
    $platnostOd = $_GET['platnost-od'];
}
if(!empty($_GET['platnost-do'])){
    $platnostDo = $_GET['platnost-do'];
}

$order = empty($_GET['order']) ? 'cisloSmlouvy' : $_GET['order'];
$orderDirection = empty($_GET['order-direction']) ? 'asc' : $_GET['order-direction'];

$stmt = mysqli_prepare($link, "SELECT
    smlouvy.id_smlouvy as id, smlouvy.cisloSmlouvy, smlouvy.typSmlouvy, typysmluv.popis, smlouvy.datumUzavreni, smlouvy.predmet, smlouvy.cena, smlouvy.velikost,
    rodneCislo, datumOd, datumDo
from smlouvy
left join typysmluv on typysmluv.id_typuSmlouvy = smlouvy.typSmlouvy
where
    (cisloSmlouvy like ? or smlouvy.predmet collate utf8_general_ci like ?)
    and typSmlouvy like ?
    and ((
        select COUNT(*) as c from smlouvystr
        join seznam_str on seznam_str.id_str = smlouvystr.id_strediska
        where
            smlouvystr.id_smlouvy = smlouvy.id_smlouvy and
            seznam_str.zkratka like ?
    ) > 0 or ? = '%')
    and datumUzavreni between ? and ?
    and datumOd <= ?
    and datumDo >= ?
order by $order $orderDirection
limit 50
");

echo mysqli_error($link);
$stmt->bind_param('sssssssss', $search, $search, $typ, $stredisko, $stredisko, $rokZacatek, $rokKonec, $platnostDo, $platnostOd);
$stmt->execute();
$result = $stmt->get_result();
echo $stmt->error;

$rows = [];
while($row = mysqli_fetch_assoc($result)){
    $row['strediska'] = mysqli_fetch_all(mysqli_query($link, "SELECT
        seznam_str.id_str as id, seznam_str.zkratka
        from smlouvystr
        join seznam_str on seznam_str.id_str = smlouvystr.id_strediska
        where smlouvystr.id_smlouvy = {$row['id']}
    "), MYSQLI_ASSOC);

    $row['partneri'] = mysqli_fetch_all(mysqli_query($link, "SELECT
        partneri.id_partnera as id, partneri.nazev
        from smlouvypartneri
        join partneri on partneri.id_partnera = smlouvypartneri.idPartnera
        where smlouvypartneri.id_smlouvy = {$row['id']}
    "), MYSQLI_ASSOC);

    $row['prilohy'] = mysqli_fetch_all(mysqli_query($link, "SELECT
        nazev, velikost
        from smlouvyprilohy
        where smlouvyprilohy.id_smlouvy = {$row['id']}
    "), MYSQLI_ASSOC);
    
    $row['faktury'] = mysqli_fetch_all(mysqli_query($link, "SELECT
        faktura, uhrazeno, concat(faktura, ', uhrazeno: ', uhrazeno) as text
        from smlouvyfak
        where smlouvyfak.id_smlouvy = {$row['id']}
    "), MYSQLI_ASSOC);

    $rows[] = $row;
}

echo json_encode($rows);