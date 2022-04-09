
<?php
require '../base.php';

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
      die('error post strediska');
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
      die('error post partneri');
    }
  }
}
function postPrilohy($idSmlouvy, $prilohy){
  global $link;

  mysqli_query($link, "delete from smlouvyprilohy where id_smlouvy = $idSmlouvy");
  foreach($prilohy as $obj){
    $stmt = mysqli_prepare($link, "insert into smlouvyprilohy (id_smlouvy, nazev, velikost) values(?,?,?)");
    echo mysqli_error($link);
    $stmt->bind_param('isi', $idSmlouvy, $obj['nazev'], $obj['velikost']);
    if(!$stmt->execute()){
      http_response_code(500);
      die('error post prilohy');
    }
  }

  uploadPrilohy($idSmlouvy);
}
function uploadPrilohy($idSmlouvy){
  if(!isset($_FILES['prilohy'])) die;

  $prilohy = $_FILES['prilohy'];
  for($i = 0; $i < count($prilohy['name']); $i++){
    $tmpName = $prilohy['tmp_name'][$i];
    $name = $prilohy['name'][$i];

    $dir = "../../prilohy/" . $idSmlouvy;
    $path = $dir . '/' . $name;

    if (!is_dir($dir)) {
      mkdir($dir);
    }

    if(!move_uploaded_file($tmpName, $path)){
      http_response_code(500);
      die('error upload prilohy');
    }
  }
}