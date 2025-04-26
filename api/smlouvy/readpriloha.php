<?php
require 'base-read.php';

if(!isset($_GET['path'])){
  return;
}
$idSmlouvy = explode('/', $_GET['path'])[0];
$name = explode('/', $_GET['path'])[1];

$stmt = mysqli_prepare($link, "select id_smlouvy, nazev, cislo, zmena > '2022-04-20' as new from smlouvyPrilohy where id_smlouvy = ? and nazev = ?");
$stmt->bind_param('is', $idSmlouvy, $name);
$stmt->execute();
$result = $stmt->get_result();

$radek = mysqli_fetch_assoc($result);

if(!$radek){
  http_response_status(404);
  die("Soubor nenalezen v databázi");
}
$filename = ($radek["cislo"] == 0 || $radek['new']) ? $radek["nazev"] : $radek["id_smlouvy"]."-".$radek["cislo"];
$extension = count(explode('.', $filename)) == 1 ? '.pdf' : '';

$path = str_replace('..', '', $prilohyPath . '/' . $idSmlouvy . '/' . $filename . $extension);

if(!file_exists($path)){
  $path = explode('.', $path)[0];
  if(!file_exists($path)){
    http_response_status(404);
    die("Soubor nenalezen");
  }
}

header('Content-Description: File Transfer');
header('Content-Disposition: attachment; filename="'.$name.'"');
header('Content-Type: application/octet-stream');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: public');
header('Content-Length: ' . filesize($path));

ob_clean();
flush();

readfile($path);
