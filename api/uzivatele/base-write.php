
<?php

require '../base.php';

if (!clearance()) {
  http_response_code(403);
  die('Neoprávněný přístup');
}

function clearance(){
  return str_contains($_SESSION['prava'], 'U');
}

function postOpravneni($userId, $opravneniStr){
  global $link;

  mysqli_query($link, "delete from pristprava where id_jmeno = $userId");

  if(empty($opravneniStr)) return true;
  $opravneni = str_split($opravneniStr);

  foreach($opravneni as $o) {
    $ok = mysqli_query($link, "insert into pristprava (id_jmeno, id_modulu, prava) values(
        $userId,
        (select id_modulu from moduly where zkratka = '$o'),
        1
      )
    ");
    if(!$ok) {
      echo 'post-opravneni: ' . mysqli_error($link);
      http_response_code(500);
      die;
      return false;
    }
  }
  return true;
}
