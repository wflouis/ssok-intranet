<?php
	include "hlava.php"; 
?>
    <div class="login obsah">
        <h2>Přihlášení do intranetu</h2>
        <form action="index.php" method="post" enctype="multipart/form-data">
        	<div class="form-group">
            	<label class="control-label" for="input-email">E-mailová adresa</label>
				<input id='input-email' class='txt txt-width100' type='email' name='email' placeholder='E-mailová adresa' />
				<p class="error"><?php echo ((!empty($_GET["error"]) and $_GET["error"] == "mail")?"Uvedná adresa není v seznamu SSOK! Kontaktujte prosím správce intranetu.":"") ; ?></p>
        	</div>
         	<div class="form-group">
           		<label class="control-label" for="input-password">Heslo (nepovinné – pokud nemáte heslo, bude na uvedný e-mail zaslán ověřovací odkaz)</label>
				<input id='input-password' class='txt txt-width100' type='password' name='heslo' placeholder='Heslo' />
				<p class="error"><?php echo ((!empty($_GET["error"]) and $_GET["error"] == "heslo")?"Heslo není správné! Zkontrolujte CapsLock a zkuste znovu. Nebo nechte heslo prázdné a bude vám zaslán ověřovací odkaz.":"") ; ?> </p>
           </div>
		   <p class="error"><?php echo ((!empty($_GET["error"]) and $_GET["error"] == "aktivace")?"Na uvedenou e-mailovou adresu byl odeslán odkaz pro ověření Vaší identity a přihlášení do intanetu. Zkontroluje svoji poštu. ":"") ; ?> </p>
		   <button class='btn login-btn'>Přihlásit se</button>
         </form>
     </div>
<?php	include "pata.php"; ?>
