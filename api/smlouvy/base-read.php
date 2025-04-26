
<?php
require dirname(__FILE__) . '/../base.php';

if (!clearance()) {
  http_response_code(403);
  die('Neoprávněný přístup');
}

function clearance(){
  return str_contains($_SESSION['prava'], 'S');
}
