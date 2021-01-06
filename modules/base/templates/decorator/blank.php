<!doctype html>
<html lang="<?= $context->getSelectedLang() ?>">
	<head>
		<meta charset="utf-8">
		<title><?= count($pageTitle) ? implode(' - ', array_reverse($pageTitle)) . ' - ' : '' ?><?= esc_html($context->getCompanyName()) ?> - Toolbox</title>

		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
		<link rel="shortcut icon" href="data:image/x-icon;," type="image/x-icon" /> 

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
			     'multiuser_check_interval' => MULTIUSER_CHECK_INTERVAL,
			     'standalone_installation' => is_standalone_installation(),
			 ])
			?>;
		</script>

		<script src="<?= appUrl('/?m=base&c=js/dynamicscripts&a=lang') ?>"></script>
		<script src="<?= BASE_HREF ?>js/jquery-3.3.1.min.js"></script>
		<script src="<?= BASE_HREF ?>js/bootstrap.min.js"></script>

		<script src="<?= BASE_HREF ?>lib/moment/moment-with-locales.min.js"></script>
		<script> moment.locale(<?= json_encode(ctx()->getSelectedLang()) ?>); </script>
		<script src="<?= BASE_HREF ?>lib/eonasdan-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
		
		
	<script src="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.min.js"></script>
	<script src="<?= BASE_HREF ?>lib/jquery.cookie.js"></script>
	<script src="<?= BASE_HREF ?>js/jquery.horizontalsplitcontainer.js"></script>
	<link href="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.theme.css" rel="stylesheet" type="text/css" />
	
	<script src="<?= BASE_HREF ?>lib/select2/js/select2.min.js"></script>
	<link href="<?= BASE_HREF ?>lib/select2/css/select2.min.css" rel="stylesheet" type="text/css" />

	<script src="<?= BASE_HREF ?>lib/nprogress/nprogress.js"></script>
		
		<script src="<?= BASE_HREF ?>js/IndexTable.js?t=<?= filemtime(WWW_ROOT.'/js/IndexTable.js') ?>"></script>
		<script src="<?= BASE_HREF ?>js/forms/form-actions.js?t=<?= filemtime(WWW_ROOT.'/js/forms/form-actions.js') ?>"></script>
		
		
		<?php if (DEBUG || file_exists(WWW_ROOT.'/css/less/style.css') == false) : ?>
		<link href="<?= BASE_HREF ?>css/less/base.less?v=<?= filemtime(WWW_ROOT.'/css/less/base.less') ?>" rel="stylesheet/less" type="text/css" />
		<script>less = { env: 'development'};</script>
		<style type="text/less">
		<?php foreach(module_less_defaults() as $lessfile) : ?>
			@import "<?= BASE_HREF . $lessfile ?>";
		<?php endforeach; ?>
		</style>
		<script src="<?= BASE_HREF ?>lib/less/dist/less.js"></script>
		<?php else : ?>
		<link href="<?= BASE_HREF ?>css/less/style.css?v=<?= filemtime(WWW_ROOT.'/css/less/style.css') ?>" rel="stylesheet" type="text/css" />
		<?php endif; ?>
		
		<script src="<?= BASE_HREF ?>js/script.js?t=<?= filemtime(WWW_ROOT.'/js/script.js') ?>"></script>
		<script src="<?= BASE_HREF ?>js/multiuser.js?t=<?= filemtime(WWW_ROOT.'/js/multiuser.js') ?>"></script>
		<script src="<?= BASE_HREF ?>js/savestate.js?t=<?= filemtime(WWW_ROOT.'/js/savestate.js') ?>"></script>
		
		<link href="<?= BASE_HREF ?>lib/font-awesome-4/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
		
		<?php print_htmlScriptLoader_top() ?>
		
		<?php print_htmlScriptLoader_inlineCss() ?>
		
	</head>
<body class="<?= isset($body_class) ? $body_class : '' ?>">

	<div class="blank-main-content">
		<?php output_user_messages() ?>
		
		<?php print $content ?>
	</div>

	<?php print_htmlScriptLoader_bottom() ?>
	
	<?php if (DEBUG) : ?>
	<script>
		function show_debug_info() {
			var eventbusEvents = <?= json_encode(@$_SESSION['debug']['eventbus-publish']) ?>;
			var serverData = <?= json_encode($_SERVER) ?>;
			var getData = <?= json_encode($_GET) ?>;
			var postData = <?= json_encode($_POST) ?>;
			var requestData = <?= json_encode($_REQUEST) ?>;
			
			<?php unset($_SESSION['debug']['eventbus-publish']); ?>

			
			show_popup( appUrl('/?m=base&c=debug&a=show_debug_info'), {
				data: {
					eventbus: eventbusEvents,
					server:   serverData,
					get:      getData,
					post:     postData,
					request:  requestData
				}
			});
		}
	</script>
	<?php endif; ?>
	
</body>
</html>
