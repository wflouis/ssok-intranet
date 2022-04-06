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
    smlouvy.id_smlouvy as id, smlouvy.cisloSmlouvy, typysmluv.popis, smlouvy.datumUzavreni, smlouvy.predmet, smlouvy.cena, smlouvy.velikost,
    (
        select GROUP_CONCAT(seznam_str.zkratka SEPARATOR '\n')
        from smlouvystr
        join seznam_str on seznam_str.id_str = smlouvystr.id_strediska
        where smlouvystr.id_smlouvy = smlouvy.id_smlouvy
    ) as strediska,
    (
        select group_concat(partneri.nazev separator '\n')
        from smlouvypartneri
        join partneri on partneri.id_partnera = smlouvypartneri.idPartnera
        where smlouvypartneri.id_smlouvy = smlouvy.id_smlouvy
    ) as partneri,
    rodneCislo, datumOd, datumDo, concat(faktura, '\nuhrazeno:\n', uhrazeno) as faktura,
    (
        select group_concat(concat(nazev, ' ', velikost) separator '\n')
        from smlouvyprilohy
        where smlouvyprilohy.id_smlouvy = smlouvy.id_smlouvy
    ) as prilohy,
    (
        select group_concat(concat(predmetZaruky, '\nod: ', datumZarukyOd, '\ndo: ', datumZarukyDo, '\n\n',
        (
            select group_concat(concat(datumKontroly, ' ', vysledekKontroly, '\nodstranění: ', datumOdstraneni) separator '\n\n')
            from kontroly
            where id_smlouvy = smlouvy.id_smlouvy and id_zaruky = zaruky.id_zaruky
        )
        ) separator '\n<hr>\n')
        from zaruky
        where id_smlouvy = smlouvy.id_smlouvy
    ) as zaruky
from smlouvy
left join typysmluv on typysmluv.id_typuSmlouvy = smlouvy.typSmlouvy
where
    (cisloSmlouvy like ? or smlouvy.predmet collate utf8_general_ci like ?)
    and typSmlouvy like ?
    and EXISTS (
        select seznam_str.zkratka from smlouvystr
        join seznam_str on seznam_str.id_str = smlouvystr.id_strediska
        where
            smlouvystr.id_smlouvy = smlouvy.id_smlouvy and
            seznam_str.zkratka like ?
    )
    and datumUzavreni between ? and ?
    and datumOd >= ? and datumOd <= ?
    and datumDo <= ? and datumDo >= ?
order by $order $orderDirection
limit 50
");

echo mysqli_error($link);
$stmt->bind_param('ssssssssss', $search, $search, $typ, $stredisko, $rokZacatek, $rokKonec, $platnostOd, $platnostDo, $platnostDo, $platnostOd);
$stmt->execute();
$result = $stmt->get_result();
echo $stmt->error;

echo json_encode(mysqli_fetch_all($result, MYSQLI_ASSOC));