<?php

session_start();
require dirname(__FILE__) . '/../databaze.php';
require dirname(__FILE__) . '/../souborypath.php';

if (!loggedIn()) {
  http_response_code(403);
  die('Nepřihlášen');
}

function loggedIn(){
  return isset($_SESSION['id_jmeno']);
}

if(!function_exists('str_contains')){
  function str_contains($haystack, $needle){
    return strpos($haystack, $needle) !== false;
  }
}

if(isset($_POST['obj'])) {
  $obj = json_decode($_POST['obj'], true);
}
else {
  $obj = json_decode(file_get_contents("php://input"), true);
}