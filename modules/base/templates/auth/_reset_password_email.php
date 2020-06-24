<?php 

$reset_url = BASE_URL.appUrl('/?m=base&c=auth&a=reset_link&id='.$reset_password_id.'&uid='.$security_string);


?><!doctype html>
<html>

<head>
</head>

<body style="font-family: Arial; font-size: 14px;">


	Er is zojuist een wachtwoord-reset aangevraagd voor de gebruiker: <?= esc_html($username) ?>
	<br/>
	<br/>
	Mocht u dit niet hebben gedaan, kunt u deze e-mail negeren.
	
	<br/>
	<br/>
	
	Om het wachtwoord te resetten klik op deze link: <a href="<?= $reset_url ?>" target="_blank"><?= $reset_url ?></a>
	
	<br/>
	<br/>
	Met vriendelijke groet,
	<br/>Toolbox
</body>




</html>


