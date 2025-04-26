<?php
require 'base-read.php';

$search = '%' . trim($_GET['search']) . '%';

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

$order = empty($_GET['order']) ? 'datumUzavreni' : $_GET['order'];
$orderDirection = empty($_GET['order-direction']) ? 'desc' : $_GET['order-direction'];

$limit = isset($_GET['limit']) ? $_GET['limit'] : 50;

$stmt = mysqli_prepare($link, "SELECT
    smlouvy.id_smlouvy as id, smlouvy.cisloSmlouvy, smlouvy.typSmlouvy, typySmluv.popis, smlouvy.datumUzavreni, smlouvy.predmet, smlouvy.cena,
    rodneCislo, datumOd, datumDo, vazba
from smlouvy
left join typySmluv on typySmluv.id_typuSmlouvy = smlouvy.typSmlouvy
where
    (cisloSmlouvy like ? or smlouvy.predmet collate utf8_general_ci like ? or smlouvy.ico like ? or rodneCislo like ? 
    or exists (
      select * from smlouvyPartneri
        join partneri on partneri.id_partnera = smlouvyPartneri.idPartnera
      where smlouvyPartneri.id_smlouvy = smlouvy.id_smlouvy
        and (partneri.ico like ? or partneri.nazev like ?)
    ))
    and typSmlouvy like ?
    and ((
        select COUNT(*) as c from smlouvyStr
        join seznam_str on seznam_str.id_str = smlouvyStr.id_strediska
        where
            smlouvyStr.id_smlouvy = smlouvy.id_smlouvy and
            seznam_str.zkratka like ?
    ) > 0 or ? = '%')
    and datumUzavreni between ? and ?
    and datumOd <= ?
    and datumDo >= ?
order by $order $orderDirection
limit ?
");

echo mysqli_error($link);
$stmt->bind_param('sssssssssssssi', $search, $search, $search, $search, $search, $search, $typ, $stredisko, $stredisko, $rokZacatek, $rokKonec, $platnostDo, $platnostOd, $limit);
$stmt->execute();
$result = $stmt->get_result();
echo $stmt->error;

$rows = [];
while($row = mysqli_fetch_assoc($result)){
    $row['strediska'] = mysqli_fetch_all(mysqli_query($link, "SELECT
        seznam_str.id_str as id, seznam_str.zkratka
        from smlouvyStr
        join seznam_str on seznam_str.id_str = smlouvyStr.id_strediska
        where smlouvyStr.id_smlouvy = {$row['id']}
    "), MYSQLI_ASSOC);

    $row['partneri'] = mysqli_fetch_all(mysqli_query($link, "SELECT
        partneri.id_partnera as id, partneri.nazev
        from smlouvyPartneri
        join partneri on partneri.id_partnera = smlouvyPartneri.idPartnera
        where smlouvyPartneri.id_smlouvy = {$row['id']}
    "), MYSQLI_ASSOC);

    $row['prilohy'] = mysqli_fetch_all(mysqli_query($link, "SELECT
        nazev, velikost
        from smlouvyPrilohy
        where smlouvyPrilohy.id_smlouvy = {$row['id']}
    "), MYSQLI_ASSOC);

    $row['faktury'] = mysqli_fetch_all(mysqli_query($link, "SELECT
        faktura, uhrazeno, concat(faktura, ', uhrazeno: ', uhrazeno) as text
        from smlouvyFak
        where smlouvyFak.id_smlouvy = {$row['id']}
    "), MYSQLI_ASSOC);

    $row['souvisejici'] = mysqli_fetch_all(mysqli_query($link, "SELECT
        id_smlouvy, cisloSmlouvy
        from smlouvy
        where vazba = {$row['vazba']} and vazba != 0 and id_smlouvy != {$row['id']}
    "), MYSQLI_ASSOC);

    $rows[] = $row;
}

echo json_encode($rows);
