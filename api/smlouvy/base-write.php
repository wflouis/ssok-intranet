
<?php
require dirname(__FILE__) . '/../base.php';

if (!clearance()) {
  http_response_code(403);
  die('Neoprávněný přístup');
}

function clearance(){
  return str_contains($_SESSION['prava'], 'M');
}

function postStrediska($idSmlouvy, $strediska){
  global $link;

  mysqli_query($link, "delete from smlouvystr where id_smlouvy = $idSmlouvy");
  foreach($strediska as $obj){
    $stmt = mysqli_prepare($link, "insert into smlouvystr (id_smlouvy, id_strediska) values(?,?)");
    $stmt->bind_param('ii', $idSmlouvy, $obj['id']);
    if(!$stmt->execute()){
      http_response_code(500);
      echo "Nepodařilo se nahrát středisko " + $obj['zkratka'];
    }
  }
}
function postPartneri($idSmlouvy, $partneri){
  global $link;

  mysqli_query($link, "delete from smlouvypartneri where id_smlouvy = $idSmlouvy");
  foreach($partneri as $obj){
    $stmt = mysqli_prepare($link, "insert into smlouvypartneri (id_smlouvy, idPartnera) values(?,?)");
    echo mysqli_error($link);
    $stmt->bind_param('ii', $idSmlouvy, $obj['id']);
    if(!$stmt->execute()){
      http_response_code(500);
      echo "Nepodařilo se nahrát partnera " + $obj['nazev'];
    }
  }
}
function postPrilohy($idSmlouvy, $prilohy){
  global $link;

  foreach($prilohy as $obj){
    $stmt = mysqli_prepare($link, "insert into smlouvyprilohy (id_smlouvy, nazev, velikost, zadal) values(?,?,?,?)");
    echo mysqli_error($link);
    $stmt->bind_param('isii', $idSmlouvy, $obj['nazev'], $obj['velikost'], $_SESSION['id_jmeno']);
    if(!$stmt->execute()){
      http_response_code(500);
      echo "Nepodařilo se zapsat přílohu " + $obj['nazev'];
    }
  }

  uploadPrilohy($idSmlouvy);
}
function deletePrilohy($idSmlouvy, $prilohy){
  global $link;

  foreach($prilohy as $obj){
    $stmt = mysqli_prepare($link, "delete from smlouvyprilohy where id_smlouvy = ? and nazev = ?");
    echo mysqli_error($link);
    $stmt->bind_param('is', $idSmlouvy, $obj['nazev']);
    if(!$stmt->execute()){
      http_response_code(500);
      echo "Nepodařilo se smazat přílohu " + $obj['nazev'];
    }

    // delete file
    unlink("../../prilohy/" . $idSmlouvy . '/' . $obj['nazev']);
  }
}
function uploadPrilohy($idSmlouvy){
  if(!isset($_FILES['prilohy'])) return;

  $prilohy = $_FILES['prilohy'];
  for($i = 0; $i < count($prilohy['name']); $i++){
    $tmpName = $prilohy['tmp_name'][$i];
    $name = $prilohy['name'][$i];

    $dir = $_SERVER['DOCUMENT_ROOT'] . '/prilohy/' . $idSmlouvy;
    $path = $dir . '/' . $name;

    if (!is_dir($dir)) {
      mkdir($dir);
    }

    if(!move_uploaded_file($tmpName, $path)){
      http_response_code(500);
      echo "Nepodařilo se nahrát přílohu " + $name;
    }
  }
}
function postFaktury($idSmlouvy, $faktury){
  global $link;

  mysqli_query($link, "delete from smlouvyfak where id_smlouvy = $idSmlouvy");
  foreach($faktury as $faktura){
    $stmt = mysqli_prepare($link, "insert into smlouvyfak (id_smlouvy, faktura, uhrazeno) values(?,?,?)");
    $stmt->bind_param('iss', $idSmlouvy, $faktura['faktura'], $faktura['uhrazeno']);
    if(!$stmt->execute()){
      http_response_code(500);
      echo "Nepodařilo se nahrát fakturu " + $faktura['faktura'];
    }
  }
}