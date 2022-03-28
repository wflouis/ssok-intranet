
<?php
require 'base.php';

if (!clearance()) {
  http_response_code(403);
  die('Neoprávněný přístup');
}

function clearance(){
  return str_contains($_SESSION['prava'], 'L');
}

function postOstatni($zkratka, $ostatni){
  global $link;

  $stmt = mysqli_prepare($link, "delete from strediska where stredisko = ?");
  $stmt->bind_param('s', $zkratka);
  $stmt->execute();

  foreach($ostatni as $o) {
    $stmt = mysqli_prepare($link, "insert into strediska values (?,?,?)");
    $stmt->bind_param('sss', $o['nadpis'], $o['text'], $zkratka);
    if(!$stmt->execute()){
      http_response_code(500);
      die('Chyba post ostatni');
    }
  }
}