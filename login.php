<?php
	include "hlava.php"; 
?>
    <div class="login obsah">
        <h2>Přihlášení do intranetu</h2>
        <form action="index.php" method="post" enctype="multipart/form-data">
        	<div class="form-group">
            	<label class="control-label" for="input-email">E-mailová adresa</label>
            	<input type="text" name="email" value="" placeholder="E-mailová adresa" id="input-email" class="form-control" />
				<p class="error"><?php echo ((!empty($_GET["error"]) and $_GET["error"] == "mail")?"Uvedná adresa není v seznamu SSOK! Kontaktujte prosím správce intranetu.":"") ; ?></p>
        	</div>
         	<div class="form-group">
           		<label class="control-label" for="input-password">Heslo (nepovinné - pokud nemáte heslo, bude na uvedný e-mail zaslán ověřovací odkaz)</label>
            	<input type="password" name="heslo" value="" placeholder="Heslo" id="input-password" class="form-control" />
				<p class="error"><?php echo ((!empty($_GET["error"]) and $_GET["error"] == "heslo")?"Heslo není správné! Zkontrolujte CapsLock a zkuste znovu. Nebo nechte heslo prázdné a bude vám zaslán ověřovací odkaz.":"") ; ?> </p>
           </div>
		   <p class="error"><?php echo ((!empty($_GET["error"]) and $_GET["error"] == "aktivace")?"Na uvedenou e-mailovou adresu byl odeslán odkaz pro ověření Vaší identity a přihlášení do intanetu. Zkontroluje svoji poštu. ":"") ; ?> </p>
           <input type="submit" value="Přihlásit se" class="btn btn-primary pull-right" />
         </form>
     </div>
<?php	include "pata.php"; ?>
