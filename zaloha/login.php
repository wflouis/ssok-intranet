<!DOCTYPE html>
<html dir="ltr" lang="cs"> 
<head>
	<meta charset="UTF-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>Přihlášení</title>
	<link href="css/bootstrap.min.css" rel="stylesheet" media="screen" />
	<link href="font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	<link href="css/stylesheet.css" rel="stylesheet">
	<script src="js/jquery-2.1.1.min.js" type="text/javascript"></script>
	<script src="js/bootstrap.min.js" type="text/javascript"></script>
	<script src="js/common.js" type="text/javascript"></script>
	<link href="images/logo.png" rel="icon" />
</head>
<body>
    <div class="login well">
 		<div class="logo"><img src="images/logotext.png" alt="Správa silnic Olomouckého kraje" border="0"></div>
		<i class="fa fa-user-circle"></i>
        <h2>Přihlášení do intranetu</h2>
        <form action="login.php" method="post" enctype="multipart/form-data">
        	<div class="form-group">
            	<label class="control-label" for="input-email">E-mailová adresa</label>
            	<input type="text" name="email" value="" placeholder="E-mailová adresa" id="input-email" class="form-control" />
				<p class="error">Uvedná adresa není v seznamu SSOK! Kontaktujte prosím správce intranetu.</p>
        	</div>
         	<div class="form-group">
           		<label class="control-label" for="input-password">Heslo (nepovinné - pokud nemáte heslo, bude na uvedný e-mail zaslán ověřovací odkaz)</label>
            	<input type="password" name="password" value="" placeholder="Heslo" id="input-password" class="form-control" />
				<p class="error">Heslo není správné! Zkontrolujte CapsLock a zkuste znovu. Nebo nechte heslo prázdné a bude vám zaslán ověřovací odkaz. </p>
           </div>
           <input type="submit" value="Přihlásit se" class="btn btn-primary pull-right" />
         </form>
     </div>
</body>
</html>
