<!DOCTYPE html>
<html dir="ltr" lang="cs"> 
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Intranet SSOK</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="css/stylesheet.css" rel="stylesheet">
	<script src="js/jquery-2.1.1.min.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<script src="js/common.js" type="text/javascript"></script>
	<script>
		<?php echo "Rozdil = new Date()-".time()."*1000;"; ?>
	</script>
	<link href="images/logo.png" rel="icon" />
</head>
<body>
    <div class="zahlavi">
 		<div class="col-sm-4"><img class="img-responsive" src="images/logoSede.png" alt="Správa silnic Olomouckého kraje" border="0"></div>
		<div class="col-sm-4 center break"><div id="AktCas" class="f21"></div><div class="f15">svátek má <?php echo (empty($_SESSION["svatek"])?"-":$_SESSION["svatek"]); ?></div></div>
		<div class="col-sm-4"><i class="fa fa-user-circle"></i> <span class="f21"><?php echo (empty($_SESSION["id_jmeno"])?"Nepřihlášen":$_SESSION["jmeno"]); ?></span></div>
    </div>
    <div class="telo">
