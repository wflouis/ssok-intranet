<?php
$loginExpire = time()+60*60*24*60;
session_set_cookie_params($loginExpire);
session_start();
include "databaze.php";

require 'mailer/PHPMailer.php';
require 'mailer/SMTP.php';
require 'mailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION["id_jmeno"])) {
	session_regenerate_id();

	if(isset($_POST['email']) || isset($_GET["kod"]) || isset($_COOKIE['intranet-token'])){
		if (!empty($_GET["kod"]))
			$podminka = $_GET["kod"];
		else if (!empty($_POST['email']))
			$podminka = $_POST['email'];
		else
			$podminka = $_COOKIE['intranet-token'];
		$Novy = $bytes = bin2hex(random_bytes(16));

		$query = "SELECT id_jmeno, jmeno, heslo, kod, ip, stredisko FROM seznam WHERE email = ? or kod = ? LIMIT 1";
		$stmt = $link->prepare($query);
		$stmt->bind_param('ss', $podminka, $podminka);
		$stmt->execute();
		$result = $stmt->get_result();

		if ($result->num_rows==1) {
			$radek = mysqli_fetch_assoc($result);
			$RIp = vratIP();
			$query = "UPDATE seznam SET kod = '$Novy', ip = '$RIp' WHERE id_jmeno = '{$radek['id_jmeno']}' LIMIT 1";

			if ($radek["kod"] == $podminka || (!empty($_POST['heslo']) && $radek["heslo"] == md5($_POST['heslo']))) {
				$_SESSION["id_jmeno"] = $radek["id_jmeno"];
				setcookie('intranet-token', $Novy, $loginExpire); // logs out in 2 months

				$_SESSION["jmeno"] = $radek["jmeno"];
				$result = mysqli_query($link,"SELECT pristup FROM seznam_str WHERE zkratka='{$radek["stredisko"]}' LIMIT 1");
				$dleStrediska = mysqli_fetch_assoc($result);
				$_SESSION["dleStrediska"] = $dleStrediska["pristup"];
				mysqli_query($link,$query);
				//  záznam historie
				$result = mysqli_query($link, "INSERT INTO historie VALUES ({$_SESSION["id_jmeno"]}, NOW())");

				if(expiringZaruky()) header('location: zaruky.php?alert');

				return;
			} else {
				if (!empty($_POST['email'])) {
					if (empty($_POST['heslo'])) {
						$handle = fopen ("hlava_reg.txt", "r");
						$zprava = "";
						mysqli_query($link,$query);
						while (!feof ($handle)) {
						    $buffer = fgets($handle);
						   	$zprava .= $buffer;
						}
						$zprava .= "http://intranet.ssok.cz/index.php?kod=".$Novy;
						fclose ($handle);

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

					    	$mail->addAddress($_POST['email']);

						$mail->isHTML(false);
						$mail->Subject = 'Aktivujte přístup do intranetu!';
						$mail->Body    = $zprava;

						$mail->send();

						$error = "aktivace";
					} else {
						$error = "heslo";
					}
				}
			}
		} else {
			if (!empty($_POST['email'])) {
				$error = "mail";
			} else
				$error = "heslo";
		}
	}
	Header("Location: login.php".(!empty($error)?"?error=$error":""));
	die;
}

if(isset($_SESSION['id_jmeno'])){
	$_SESSION["prava"] = "";
	$result = mysqli_query($link,"SELECT zkratka FROM opravneni_moduly m, opravneni p WHERE m.id_modulu = p.id_modulu and id_jmeno = '{$_SESSION["id_jmeno"]}'");
	while($zkratky=mysqli_fetch_assoc($result)) $_SESSION["prava"] .= $zkratky["zkratka"];
}

function vratIP() {
     if (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER)){
            return  $_SERVER["HTTP_X_FORWARDED_FOR"];
     }else if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            return $_SERVER["REMOTE_ADDR"];
     }else if (array_key_exists('HTTP_CLIENT_IP', $_SERVER)) {
            return $_SERVER["HTTP_CLIENT_IP"];
     }
     return '';
}

function expiringZaruky(){
	global $link;

	$result = mysqli_query($link, "select datumZarukyDo from ssok.zaruky where zadal = {$_SESSION['id_jmeno']} and (datumZarukyDo >= current_date and datumZarukyDo <= DATE_ADD(current_date, INTERVAL 30 DAY))");

	return $result->num_rows > 0;
}

function tableText(){
	return "<div class='table-text'>Změna se provádí dvojitým kliknutím přímo v tabulce
	<br>Horizontálně je možné tabulku posouvat stlačením kolečka myši</div>";
}
?>
