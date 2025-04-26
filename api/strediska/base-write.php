
<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
require '../base.php';

if (!clearance()) {
  http_response_code(403);
  die('Neoprávněný přístup');
}

function clearance(){
  return str_contains($_SESSION['prava'], 'U');
}

function postOstatni($idstr, $zkratka, $ostatni){
  global $link;

  $stmt = mysqli_prepare($link, "delete from strediska where id_str = ?");
  $stmt->bind_param('i', $idstr);
  $stmt->execute();

  foreach($ostatni as $o) {
    $stmt = mysqli_prepare($link, "insert into strediska (nadpis,text,stredisko,id_str) values (?,?,?,?)");
    $stmt->bind_param('sssi', $o['nadpis'], $o['text'], $zkratka, $idstr);
    if(!$stmt->execute()){
      http_response_code(500);
      die('Chyba post ostatni');
    }
  }
}
