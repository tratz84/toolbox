<!doctype html>
<html lang="nl">
	<head>
		<meta charset="utf-8">
		<title></title>

		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		
		<meta name="robots" content="noindex,nofollow" />
		
		<script src="<?= BASE_HREF ?>lib/mobile-detect.min.js"></script>
		<script>
			var contextName = <?= json_encode($context->getContextName()) ?>;
			var username = <?= json_encode($context->getUser() ? $context->getUser()->getUsername() : '') ?>;
			var multiuser_check_interval = <?= MULTIUSER_CHECK_INTERVAL ?>;

			var appSettings = <?php
			 print json_encode([
			     'base_href' => BASE_HREF,
			     'contextName' => $context->getContextName(),
			     'appRootUrl' => appUrl('/'),
			     'username' => $context->getUser() ? $context->getUser()->getUsername() : '',
			     'lang' => $context->getSelectedLang(),
			     'multiuser_check_interval' => MULTIUSER_CHECK_INTERVAL,
			     'standalone_installation' => is_standalone_installation(),
			     'currency_symbol' => TOOLBOX_CURRENCY_SYMBOL
			 ])
			?>;

			appSettings.is_mobile = new MobileDetect( window.navigator.userAgent ).phone() !== null ? 1 : 0;

		</script>

		<script src="<?= BASE_HREF ?>js/jquery-3.3.1.min.js"></script>
		<script src="<?= BASE_HREF ?>js/jquery-migrate-3.0.0.min.js"></script>
		<script src="<?= BASE_HREF ?>js/bootstrap.min.js?v=<?= filemtime(WWW_ROOT.'/js/bootstrap.min.js') ?>"></script>

		<script src="<?= BASE_HREF ?>lib/moment/moment-with-locales.min.js"></script>
		<script> moment.locale(<?= json_encode(ctx()->getSelectedLang()) ?>); </script>
		<script src="<?= BASE_HREF ?>lib/eonasdan-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
		
		
	<script src="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.min.js"></script>
	<script src="<?= BASE_HREF ?>lib/jquery.cookie.js"></script>
	<script src="<?= BASE_HREF ?>js/jquery.horizontalsplitcontainer.js"></script>
	<link href="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.theme.css" rel="stylesheet" type="text/css" />
	
	<script src="<?= BASE_HREF ?>lib/jquery.ui.touch-punch.js"></script>
	
	<script src="<?= BASE_HREF ?>lib/select2/js/select2.min.js"></script>
	<link href="<?= BASE_HREF ?>lib/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

	<script src="<?= BASE_HREF ?>js/eztemplate/EzTemplateLoader.js"></script>
	<script src="<?= BASE_HREF ?>js/eztemplate/EzTemplate.js"></script>


	<script src="<?= BASE_HREF ?>lib/nprogress/nprogress.js"></script>
		
		<script src="<?= BASE_HREF ?>js/script.js?t=<?= filemtime(WWW_ROOT.'/js/script.js') ?>"></script>
		<script src="<?= BASE_HREF ?>js/img-rotate.js?t=<?= filemtime(WWW_ROOT.'/js/img-rotate.js') ?>"></script>
		
		<link href="<?= BASE_HREF ?>lib/fontawesome-free-5.15.3-web/css/v4-shims.min.css" rel="stylesheet" type="text/css" />
		<link href="<?= BASE_HREF ?>lib/fontawesome-free-5.15.3-web/css/all.min.css" rel="stylesheet" type="text/css" />
		
		
		
	</head>
<body class="<?= isset($body_class) ? $body_class : '' ?>">
	
	<div class="main-content">
		<?php output_user_messages() ?>
		
		<?php print $content ?>
	</div>

	
	
</body>
</html>

