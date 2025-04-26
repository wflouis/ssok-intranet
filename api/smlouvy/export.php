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
(SELECT zkratka FROM seznam_str se JOIN smlouvyStr sm ON id_strediska = id_str WHERE sm.id_smlouvy = smlouvy.id_smlouvy LIMIT 1) as stredisko, 
smlouvy.id_smlouvy, cisloSmlouvy, datumUzavreni, ico, (select nazev from partneri WHERE partneri.ico = smlouvy.ico LIMIT 1) as partner, rodneCislo,	predmet, cena, datumTxt, datumOd, datumDo, z.id_zaruky, predmetZaruky, datumZarukyOd,	datumZarukyDo, id_kontroly, vysledekKontroly, datumKontroly, zavady, datumOdstraneni, 
(select jmeno from seznam where z.zadal = seznam.id_jmeno LIMIT 1) as jmeno 
FROM smlouvy  
LEFT JOIN zaruky z ON smlouvy.id_smlouvy = z.id_smlouvy 
LEFT JOIN kontroly k ON k.id_smlouvy = z.id_smlouvy and k.id_zaruky = z.id_zaruky
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
    $rows[] = $row;
}

echo json_encode($rows);
