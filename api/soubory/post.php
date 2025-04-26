<?php

require 'basewrite.php';

$path = $souboryPath . $_GET['path'];
$files = $_FILES['files'];

for($i = 0; $i < count($files['name']); $i++){
  if(!move_uploaded_file($files['tmp_name'][$i], $path . $files['name'][$i])){
    echo "Failed to upload \"{$files['name'][$i]}\"\n";
  }
}
