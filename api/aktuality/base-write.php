
<?php
require '../base.php';

require '../../mailer/PHPMailer.php';
require '../../mailer/SMTP.php';
require '../../mailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!clearance()) {
  http_response_code(403);
  die('Neoprávněný přístup');
}

function clearance(){
  return str_contains($_SESSION['prava'], 'A');
}

function sendEmails($obj){
  global $link;

  $users = $obj['usersMails'];
  $strediska = $obj['strediskaMails'];

  $author = $_SESSION['jmeno'];
  $text = $obj['text'];

  $body = "Zpráva od $author:<br><br>$text";

  $usersFormatted = "'" . join("','", $users) . "'";
  $strediskaFormatted = "'" . join("','", $strediska) . "'";

  $sql = "SELECT distinct email from seznam where ";
  
  if(count($strediska) > 0 && count($users) > 0)
    $sql .= "stredisko IN ($strediskaFormatted) OR id_jmeno IN ($usersFormatted) ";
  else if(count($strediska) > 0)
    $sql .= "stredisko IN ($strediskaFormatted) ";
  else if(count($users) > 0)
    $sql .= "id_jmeno IN ($usersFormatted) ";

  $result = mysqli_query($link, $sql);
  echo mysqli_error($link);

  $addresses = [];
  while($row = mysqli_fetch_assoc($result)){
    $addresses[] = $row['email'];
  }

  sendEmail($body, $addresses);
}

function sendEmail($body, $addresses){
  $mail = new PHPMailer(true);

  $mail->isSMTP();
  $mail->CharSet    = 'UTF-8';
  $mail->Host       = 'smtp.profiwh.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'noreply@scomeq.cz';
  $mail->Password   = 'lkwzk1r';
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
  $mail->Port       = 587;

  $mail->setFrom('noreply@aito.cz', 'Správa silnic Olomouckého kraje');

  print_r($addresses);
  foreach($addresses as $key => $address){
    $mail->addAddress($address);
  }

  $mail->isHTML(true);
  $mail->Subject = 'Zpráva z SSOK';
  $mail->Body    = $body;

  $mail->send();
}