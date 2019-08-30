<?php

$ms = $oc->get(\base\service\MenuService::class);
$menuItems = $ms->listMainMenu();


?><!doctype html>
<html lang="nl">
	<head>
		<meta charset="utf-8">
		<title>Insights - <?= esc_html($context->getCompanyName()) ?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

		<script>
			var contextName = <?= json_encode($context->getContextName()) ?>;
			var username = <?= json_encode($context->getUser() ? $context->getUser()->getUsername() : '') ?>;
			var multiuser_check_interval = <?= MULTIUSER_CHECK_INTERVAL ?>;

			var appSettings = <?php
			 print json_encode([
			     'base_href' => BASE_HREF,
			     'contextName' => $context->getContextName(),
			     'username' => $context->getUser() ? $context->getUser()->getUsername() : '',
			     'multiuser_check_interval' => MULTIUSER_CHECK_INTERVAL,
			     'standalone_installation' => is_standalone_installation(),
			 ])
			?>;
		</script>

		<script src="<?= appUrl('/?m=base&c=generatedjs&a=lang') ?>"></script>
		<script src="<?= BASE_HREF ?>js/jquery-3.3.1.min.js"></script>
		<script src="<?= BASE_HREF ?>js/bootstrap.min.js"></script>

		<script src="<?= BASE_HREF ?>lib/moment/moment-with-locales.min.js"></script>
		<script src="<?= BASE_HREF ?>lib/eonasdan-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
		
<!-- 		<script src="/lib/pickadate-3.5.6/picker.js"></script> -->
<!-- 		<script src="/lib/pickadate-3.5.6/picker.date.js"></script> -->
<!-- 		<script src="/lib/pickadate-3.5.6/picker.time.js"></script> -->
<!-- 		<link href="/lib/pickadate-3.5.6/themes/classic.css" rel="stylesheet" type="text/css" /> -->
<!-- 		<link href="/lib/pickadate-3.5.6/themes/classic.date.css" rel="stylesheet" type="text/css" /> -->
<!-- 		<link href="/lib/pickadate-3.5.6/themes/classic.time.css" rel="stylesheet" type="text/css" /> -->
<!-- 		<script src="/lib/pickadate-3.5.6/translations/nl_NL.js"></script> -->
		
		
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
    <header>
        <div class="notifications-bar">
            <div class="notifications-right">
                <span class="current-user"><?= $context->getUser() ?></span>
                <a href="<?= appUrl('/?m=base&c=auth&a=logoff') ?>" class="fa fa-sign-out" title="Afmelden"></a>
            </div>
            <div class="administration-name">
	            <a href="javascript:void(0);" class="nav-side-menu-toggle fa fa-caret-left" onclick="navSideMenu_toggle();"></a>
            
            	<div class="administration-name"><a href="<?= appUrl('/') ?>" title="Dashboard"><?= esc_html($context->getCompanyName()) ?></a></div>
            </div>
        </div>
    </header>
	<div class="nav-side-menu">
		<div class="mobile-menu-header d-md-none"><a href="<?= appUrl('/') ?>"><?= esc_html($context->getCompanyName()) ?></a></div>
		
		<div class="mobile-icon-container"></div>
		
		<div class="menu-mobile-spacer"></div>
		
	    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
	
		<div class="menu-list">
			

			<ul id="menu-content" class="menu-content collapse out">
			<?php $activeSet = false ?>
    		<?php if (isset($menuItems)) foreach($menuItems as $mi) : ?>
    			<?php
    			 if ($activeSet == false && $mi->isActive()) {
    			     $active = true;
    			     $activeSet = true;
    			 } else {
        			 $active = false;
    			 }
    			?>
				<li class="menu-item">
					<a class="nav-link <?= $active ? 'active' : '' ?> weight-<?= $mi->getWeight() ?>" href="<?= appUrl($mi->getUrl()) ?>">
						<i class="fa <?= $mi->getIcon() ?>"></i> 
						<span class="menu-label"><?= esc_html($mi->getLabel()) ?></span>
					</a>
					
					<?php if ($mi->hasChildMenus()) : ?>
					<?php $childItems = $mi->getChildMenus() ?>
					<?php if ($mi->menuAsFirstChild()) $childItems = array_merge(array($mi), $childItems) ?>
					<ul class="child-menu">
    					<?php foreach($childItems as $ci) : ?>	
    					<li>
    						<a class="nav-link weight-<?= $ci->getWeight() ?>" href="<?= appUrl($ci->getUrl()) ?>">
        						<i class="fa <?= $ci->getIcon() ?>"></i> 
        						<span class="menu-label"><?= esc_html($ci->getLabel()) ?></span>
        					</a>
    					</li>
    					<?php endforeach; ?>
					</ul>
					<?php endif; ?>
				</li>
    		<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="main-content">
		<?php output_user_messages() ?>
		
		<?php output_user_errors() ?>
	
		<?php print $content ?>
	</div>

	<?php print_htmlScriptLoader_bottom() ?>
	
</body>
</html>
