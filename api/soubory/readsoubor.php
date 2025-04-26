<?php

require '../base.php';
include '../../souborypath.php';

if(!isset($_GET['path'])){
  return;
}

$path = str_replace('..', '', $souboryPath . '/' . $_GET['path']);

$pathExploded = explode('/', $path);
$name = $pathExploded[count($pathExploded) - 1];

if(!file_exists($path)){
  http_response_status(404);
  die("Soubor nenalezen");
}

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="'.$name.'"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($path));

ob_clean();
flush();

readfile($path);
