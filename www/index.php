<?php 

require_once '../config/config.php';

if (is_installation_mode()) {
    \core\Context::getInstance()->enableModule('codegen');
    include_component('codegen', 'install/wizard', 'index', array('showDecorator' => false));
    exit;
}

if (is_standalone_installation()) {
    include __DIR__.'/start.php';
    exit;
}

if (is_post()) {
    require_once ROOT.'/modules/admin/lib/service/GlobalLoginService.php';
    $gls = new admin\service\GlobalLoginService();
    if (isset($_REQUEST['code']) && $customer = $gls->contextExists($_REQUEST['code'])) {
        header('Location: '.BASE_HREF.$customer->getContextName().'/');
    } else {
        $msg = 'administratie niet gevonden';
    }
}


?><!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>itxplain - Toolbox</title>

		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="icon" type="image/x-icon" href="favicon.ico" />
		<link href="<?= BASE_HREF ?>css/bootstrap/bootstrap.min.css" type="text/css" rel="stylesheet" />

	</head>
<body>

	<form method="post" action="">
    	<div style="text-align: center; margin-top: 10em;">
    		<img src="<?= BASE_HREF ?>images/itxplain-logo.png" style="max-width: 80%;" />
    
    		<br/><br/><br/><br/>
    		Ga naar uw administratie, https://<?= $_SERVER['HTTP_HOST'] ?>/<input type="text" id="code" name="code" value="<?= htmlentities(@$_REQUEST['code'], ENT_COMPAT, 'UTF-8') ?>" /><input type="submit" value="&gt;" />
    		<?php if (isset($msg)) : ?>
    		<div style="color: #f00; font-style: italic; "><?= $msg ?></div>
    		<?php endif; ?>
    	</div>
    </form>
	
	<script>

		document.getElementById('code').focus();
	</script>	

</body>
</html>
