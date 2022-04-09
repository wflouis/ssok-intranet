<?php

session_start();
require '../../databaze.php';

if (!loggedIn()) {
  http_response_code(403);
  die('Nepřihlášen');
}

function loggedIn(){
  return isset($_SESSION['id_jmeno']);
}

if(isset($_POST['obj'])) {
  $obj = json_decode($_POST['obj'], true);
}
else {
  $obj = json_decode(file_get_contents("php://input"), true);
}