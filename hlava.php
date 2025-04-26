<!DOCTYPE html>
<html dir="ltr" lang="cs"> 
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Intranet SSOK</title>

	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" />
	<link href="css/index.css" rel="stylesheet">

	<script src="js/jquery-2.1.1.min.js" type="text/javascript"></script>
	<script src="js/common.js" type="text/javascript"></script>
	<script>
		<?php echo "Rozdil = new Date()-".time()."*1000;"; ?>

		// setInterval(function(){location.reload(true);}, 2000);
	</script>
	<link href="images/logo.png" rel="icon" />
</head>
<body>
  <div class="header">
    <?php
      if(isset($homePage)){
        echo '<div id="menu-button" class="header-menu menu-open">
          <i class="fa fa-solid fa-2x header-menu-open-icon"></i>
          <i class="fa fa-solid fa-2x header-menu-close-icon"></i>
        </div>';
      }
    ?>
    <img class="header-logo" src="images/logoSede.png" alt="Správa silnic Olomouckého kraje">
    <div class="sgap"></div>
    <?php 
    if(isset($homePage)){
      $result = mysqli_query($link,"SELECT svatek FROM svatky WHERE mesic = '".date("n")."' and den = '".date("d")."'");
      $radek = mysqli_fetch_assoc($result);
      $svatek = $radek["svatek"];
      
      echo "
      <div class='header-center'>
        <div id='AktCas' class='header-time'></div>
          <div class='header-nameday'>svátek má $svatek</div>
        </div>
      <div class='sgap'></div>";
    }
    ?>
    <div class="header-account">
      <div class='header-account-info'>
        <i class="fa fa-user-circle fa-2x"></i>
        <span class="header-account-name"><?php echo (empty($_SESSION["id_jmeno"])?"Nepřihlášen":$_SESSION["jmeno"]); ?></span>
      </div>
      <?=(isset($_SESSION['id_jmeno']) ? "<a class='header-logout icon logout-icon' href='logout.php'></a>" : '')?>
    </div>
  </div>
  <div class="page">
