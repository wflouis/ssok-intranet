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