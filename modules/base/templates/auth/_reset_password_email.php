<?php

$reset_url = BASE_URL.appUrl('/?m=base&c=auth&a=reset_link&id='.$reset_password_id.'&uid='.$security_string);


?><!doctype html>
<html>

<head>
</head>

<body style="font-family: Arial; font-size: 14px;">


	A password reset has just been requested for the user: <?= esc_html($username) ?>
	<br/>
	<br/>
	If you didn't, you can ignore this email.
	
	<br/>
	<br/>
	
	Link for resetting your password: <a href="<?= $reset_url ?>" target="_blank"><?= $reset_url ?></a>
	
	<br/>
	<br/>
	Kind regards,
	<br/>Toolbox
</body>




</html>


