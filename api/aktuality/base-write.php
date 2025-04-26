
<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

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

  $sql = "SELECT distinct email from seznam where email > '' ";

  if(count($strediska) > 0 && count($users) > 0)
    $sql .= "and (stredisko IN ($strediskaFormatted) OR id_jmeno IN ($usersFormatted)) ";
  else if(count($strediska) > 0)
    $sql .= "and stredisko IN ($strediskaFormatted) ";
  else if(count($users) > 0)
    $sql .= "and id_jmeno IN ($usersFormatted) ";
  else $sql .= "and false";

  $result = mysqli_query($link, $sql);
  echo mysqli_error($link);

  $addresses = [];
  while($row = mysqli_fetch_assoc($result)){
    $addresses[] = $row['email'];
  }

  $invalidAddresses = sendEmail($body, $addresses);
  return $invalidAddresses;
}

function sendEmail($body, $addresses){
  if (empty($addresses)) {
    return null;
  }
  $mail = new PHPMailer(true);

  $mail->isSMTP();
  $mail->CharSet    = 'UTF-8';
  $mail->Host       = 'smtp.profiwh.com';
  $mail->SMTPAuth   = true;
  $mail->Username   = 'ssok@ssok.cz';
  $mail->Password   = 'lipno';
  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
  $mail->Port       = 587;

  $mail->setFrom('ssok@ssok.cz', 'Správa silnic Olomouckého kraje');

  // print_r($addresses);
  $invalidAddresses = [];
  foreach($addresses as $key => $address){
    try{
      $mail->addAddress($address);
    }
    catch(Exception $e) {
      $invalidAddresses[] = $address;
    }
  }

  $mail->isHTML(true);
  $mail->Subject = 'Zpráva z intranetového portálu SSOK';
  $mail->Body    = $body;

  $mail->send();

  return $invalidAddresses;
}
