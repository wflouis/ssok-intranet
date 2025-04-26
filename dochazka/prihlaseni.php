<?php

include "databaze.php"; 

session_start(); 

if (isset($_POST['osCislo']) && !empty($_POST['heslo'])) { 

	$query = "SELECT * FROM DochZamestnanci WHERE osCislo='".$_POST['osCislo']."' LIMIT 1";

    $result = mysqli_query($spojeni, $query); 

	if (mysqli_num_rows($result)==1 && $radek=mysqli_fetch_assoc($result)) { 

		if ($_POST['heslo'] == $radek["heslo"] or empty($radek["heslo"])) { 

			if (empty($radek["ip"])) {

				@mysqli_query($spojeni, "update DochZamestnanci set ip='".vratIP()."' where id='".$radek["id"]."'"); 

			}

			$povoleneIP = mysqli_query($spojeni, "SELECT count( ip ) as pocet FROM `DochZamestnanci` WHERE ip = '".vratIP()."'");

			$vysledek = mysqli_fetch_assoc($povoleneIP);

			if ($vysledek["pocet"]==0 and $_POST['osCislo']>0) {

				Header("Location: chyba.html");

				exit;

			}

			$_SESSION["user"] = $radek["id"];

			$_SESSION["cas"] = mktime();

			$_SESSION["posun"] = date("n"); 



			unset($_SESSION["id"]);

			if ($_SESSION["user"]==1 or isset($_SESSION["administrator"])) {

				$_SESSION["administrator"] = 1;

				$_SESSION["user"] = "";

				Header("Location: administrace.php");

			} else 

				Header("Location: pichacky.php"); 

			exit;

		} else {

			unset($_SESSION["administrator"]);

			unset($_SESSION["user"]);

		}

	}

}

Header("Location: index.html");



function vratIP() {

	$RIp = array();

    if  (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && strpos($_SERVER['HTTP_X_FORWARDED_FOR'],',')) {

        $RIp +=  explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']);

    } elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {

        $RIp[] = $_SERVER['HTTP_X_FORWARDED_FOR'];

    }

    $RIp[] = $_SERVER['REMOTE_ADDR'];

    return $RIp[0];

}

?>

