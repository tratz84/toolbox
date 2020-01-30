<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Toolbox</title>

		<base href="<?= BASE_HREF ?>" />

		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
		<link rel="icon" type="image/x-icon" href="favicon.ico">
		
		<script>
			var appSettings = <?php
				 print json_encode([
				     'base_href' => '/',
				     'contextName' => $context->getContextName(),
				     'multiuser_check_interval' => MULTIUSER_CHECK_INTERVAL,
				     'standalone_installation' => is_standalone_installation(),
				 ])
				?>;

		</script>

		<script src="<?= BASE_HREF ?>js/jquery-3.3.1.min.js" type="text/javascript"></script>
		<script src="<?= BASE_HREF ?>js/bootstrap.min.js" type="text/javascript"></script>
		<script src="<?= BASE_HREF ?>js/script.js" type="text/javascript"></script>
		
		<?php if (DEBUG || file_exists(WWW_ROOT.'/css/less/style.css') == false) : ?>
		<link href="<?= BASE_HREF ?>css/less/base.less?v=<?= filemtime(WWW_ROOT.'/css/less/base.less') ?>" rel="stylesheet/less" type="text/css" />
		<script>less = { env: 'development'};</script>
		<script src="<?= BASE_HREF ?>lib/less/dist/less.js" type="text/javascript"></script>
		<?php else : ?>
		<link href="<?= BASE_HREF ?>css/less/style.css?v=<?= filemtime(WWW_ROOT.'/css/less/style.css') ?>" rel="stylesheet" type="text/css" />
		<?php endif; ?>
		
		
		<link href="<?= BASE_HREF ?>lib/font-awesome-4/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	</head>
<body class="auth">

	<div class="main-content">
		<?php print $content ?>
	</div>

</body>
</html>
