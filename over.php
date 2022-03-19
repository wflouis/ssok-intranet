<?php
session_start();
include "databaze.php"; 
if (!isset($_SESSION["uzivatel"])) {
	if (isset($_POST['email']) || isset($_COOKIE["intranet"]) || isset($_GET["kod"])) {
		if (!empty($_GET["kod"]))
			$podminka = $_GET["kod"];
		else if (!empty($_POST['email']))
			$podminka = $_POST['email'];
		else if (!empty($_COOKIE["intranet"]))
			$podminka = $_COOKIE["intranet"];
		else 
			$podminka = md5(rand());
		setcookie("intranet", "", time()- 3600*24);
		$Novy = md5(rand());
		$query = "SELECT id_jmeno, jmeno, heslo, kod, ip, stredisko FROM seznam WHERE email='$podminka' or kod = '$podminka' LIMIT 1";
		$result = mysqli_query($link,$query);  //echo $query;
		if (mysqli_num_rows($result)==1) {
			$radek = mysqli_fetch_assoc($result);
			$RIp = vratIP();
			$query = "UPDATE seznam SET kod = '$Novy', ip = '".$RIp."' WHERE id_jmeno = '".$radek["id_jmeno"]."' LIMIT 1";
			if ($radek["kod"] == $podminka || (!empty($_POST['heslo']) && $radek["heslo"] == md5($_POST['heslo']))) {
				$_SESSION["prava"] = "X"; 
				$result = mysqli_query($link,"SELECT zkratka FROM moduly m, pristPrava p WHERE m.id_modulu = p.id_modulu and id_jmeno = '".$radek["id_jmeno"]."'");
				while($zkratky=mysqli_fetch_assoc($result))
					$_SESSION["prava"] .= $zkratky["zkratka"];
				setcookie("intranet", $Novy, time()+ 3600*24*365);
				setcookie("id_jmeno", $radek["id_jmeno"], time()+ 3600*24*365);
				$_SESSION["id_jmeno"] = $radek["id_jmeno"];
				$_SESSION["jmeno"] = $radek["jmeno"];
				$result = mysqli_query($link,"SELECT pristup FROM seznam_str WHERE zkratka='".$radek["stredisko"]."' LIMIT 1"); 
				$dleStrediska = mysqli_fetch_assoc($result);
				$_SESSION["dleStrediska"] = $dleStrediska["pristup"]; 
				mysqli_query($link,$query);
				//  záznam historie
			    $result = mysqli_query($link,"INSERT INTO historie VALUES ('".$_SESSION["id_jmeno"]."',NOW())");
  				$result = mysqli_query($link,"SELECT svatek FROM svatky WHERE mesic = '".date("n")."' and den = '".date("d")."'");
				$radek = mysqli_fetch_assoc($result); 
  				$_SESSION["svatek"] = $radek["svatek"];
				return;
			} else {
				if (!empty($_POST['email'])) {
					if (empty($_POST['heslo'])) {
		//				$handle = fopen ("hlava_reg.html", "r");
						$handle = fopen ("hlava_reg.txt", "r");
						$zprava = "";
						mysqli_query($link,$query);
						while (!feof ($handle)) {
						    $buffer = fgets($handle);
						   	$zprava .= $buffer;
						}
		//				$zprava .= "<input type=\"hidden\" name=\"kod\" value=\"$Novy\"></form></body></html>";
//						$zprava .= "http://intranet.ssok.cz/over.php?kod=$Novy";
						$zprava .= "http://localhost/intranet-novy/index.php?kod=$Novy";
						fclose ($handle);
						$headers = "From: Intranet <ulmann@aito.cz>\n";
						$headers .= "X-Sender: <ulmann@aito.cz>\n";
						$headers .= "X-Mailer: PHP\n"; 
						$headers .= "X-Priority: 1\n"; 
						$headers .= "Return-Path: <ulmann@aito.cz>\n";  
		//				$headers .= "Content-Type: text/html; charset=Windows-1250\n"; 
						$headers .= "Content-Type: text/plain; charset=Windows-1250\n"; 
						mail($_POST['email'], "Aktivujte pøístup do intranetu!", $zprava, $headers);
						$error = "aktivace";
					} else {
						$error = "heslo";
					}
				} 
			}
		} else {
			if (!empty($_POST['email'])) {
				$error = "mail";
			}
		}
	}
	Header("Location: login.php".(!empty($error)?"?error=$error":""));
	exit;
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

?>
