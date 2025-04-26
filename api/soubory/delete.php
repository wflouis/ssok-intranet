<?php

require 'basewrite.php';

$path = $souboryPath . $_GET['path'];

if(!unlink($path)){
  echo 'Nepodařilo se odstranis soubor';
}
