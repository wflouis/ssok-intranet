<?php

require '../base.php';

if (!clearance()) {
  http_response_code(403);
  die('Neoprávněný přístup');
}
function clearance(){
  return str_contains($_SESSION['prava'], 'I');
}

$path = $_SERVER['DOCUMENT_ROOT'] . '/soubory' . $_GET['path'];

if(!unlink($path)){
  echo 'Nepodařilo se odstranis soubor';
}