<?php

require '../base.php';

if (!clearance()) {
  http_response_code(403);
  die('Neoprávněný přístup');
}
function clearance(){
  return str_contains($_SESSION['prava'], 'I');
}

$path = $_SERVER['DOCUMENT_ROOT'] . '/soubory/' . $_GET['path'];
$files = $_FILES['files'];

for($i = 0; $i < count($files['name']); $i++){
  if(!move_uploaded_file($files['tmp_name'][$i], $path . $files['name'][$i])){
    echo "Failed to upload \"{$files['name'][$i]}\"\n";
  }
}