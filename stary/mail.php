<?php
			$zprava = "Ahoj";
			$headers .= "From: Intranet <ulmann@scomeq.cz>\n";
			$headers .= "X-Sender: <ulmann@scomeq.cz>\n";
			$headers .= "X-Mailer: PHP\n"; 
			$headers .= "X-Priority: 1\n"; 
			$headers .= "Return-Path: <ulmann@scomeq.cz>\n";  
			$headers .= "Content-Type: text/html; charset=Windows-1250\n"; 
			mail("ulmann@florplant.cz", "Aktivujte pøístup do programu!", $zprava, $headers);
?>		
<html><body>hotovo	</body></html>


