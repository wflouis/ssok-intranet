<?php
ini_set('default_charset', 'windows-1250');
$spojeni = mysqli_connect("localhost", "root", "Scom15451242") or die("Spojení se serverem selhalo. Zkuste to prosím pozdìji!");
mysqli_query($spojeni, "SET NAMES cp1250");
mysqli_select_db($spojeni, "dochazka") or die("Požadovaná databáze nenalezena. Zkuste to prosím pozdìji!");

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

$mesice = array("ledna","února","bøezna","dubna","kvìtna","èervna","èervence","srpna","záøí","øíjna","listopadu","prosince");
$mesiceL = array("leden","únor","bøezen","duben","kvìten","èerven","èervenec","srpen","záøí","øíjen","listopad","prosinec");
$dny = array("nedìle","pondìlí","úterý","støeda","ètvrtek","pátek","sobota");
$dnyZkr = array("Ne","Po","Út","St","Èt","Pá","So");
?>