<?php
ini_set('default_charset', 'windows-1250');
$spojeni = mysqli_connect("localhost", "root", "Scom15451242") or die("Spojen� se serverem selhalo. Zkuste to pros�m pozd�ji!");
mysqli_query($spojeni, "SET NAMES cp1250");
mysqli_select_db($spojeni, "dochazka") or die("Po�adovan� datab�ze nenalezena. Zkuste to pros�m pozd�ji!");

$mesic = date("n",mktime(0, 0, 0, $_SESSION["posun"], 1, date("Y")));
$rok   = date("Y",mktime(0, 0, 0, $_SESSION["posun"], 1, date("Y")));

$datumOd = date("Y-m-d",mktime(0, 0, 0, $mesic, 1, $rok));
$datumDo = date("Y-m-d",mktime(0, 0, 0, $mesic+1, 1, $rok));
$datumOdPrev = date("Y-m-d",mktime(0, 0, 0, $mesic-1, 1, $rok));
$datumDoPrev = date("Y-m-d",mktime(0, 0, 0, $mesic, 1, $rok));
if (mktime(0, 0, 0, $mesic, 1, $rok)<time() and time()<mktime(0, 0, 0, $mesic+1, 1, $rok))
	$dnes = date("j");
else
	$dnes = date("j",mktime(0, 0, 0, $mesic+1, 0, $rok));

$mesice = array("ledna","�nora","b�ezna","dubna","kv�tna","�ervna","�ervence","srpna","z���","��jna","listopadu","prosince");
$mesiceL = array("leden","�nor","b�ezen","duben","kv�ten","�erven","�ervenec","srpen","z���","��jen","listopad","prosinec");
$dny = array("ned�le","pond�l�","�ter�","st�eda","�tvrtek","p�tek","sobota");
$dnyZkr = array("Ne","Po","�t","St","�t","P�","So");
?>