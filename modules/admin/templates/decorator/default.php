<!doctype html>
<html lang="nl">
	<head>
		<meta charset="utf-8">
		<title>Toolbox - <?= esc_html($ctx->getContextName()) ?></title>

		<meta name="viewport" content="width=device-width, initial-scale=1">

		<script>
			var contextName = 'admin';
			var appSettings = <?= json_encode([
			    'base_href' => BASE_HREF,
			    'contextName' => 'admin',
			    'appRootUrl' => BASE_HREF.'admin/',
			    'multiuser_check_interval' => MULTIUSER_CHECK_INTERVAL,
			    'standalone_installation' => false
			]) ?>;
			
			// dynamicscriptsController is not loaded in admin-mod
			var t = function(str) { return str; };
			var toolbox_t = function(str) { return str; };
		</script>

		<script src="/js/jquery-3.3.1.min.js" type="text/javascript"></script>
		<script src="/js/bootstrap.min.js" type="text/javascript"></script>
		
	<script src="/lib/jquery-ui/jquery-ui.min.js?t=1538721029369"></script>
	<link href="/lib/jquery-ui/jquery-ui.min.css?t=1538721029369" rel="stylesheet" type="text/css" />
	<link href="/lib/jquery-ui/jquery-ui.structure.min.css?t=1538721029369" rel="stylesheet" type="text/css" />
	<link href="/lib/jquery-ui/jquery-ui.theme.css?t=1538721029369" rel="stylesheet" type="text/css" />
	
	<script src="/lib/nprogress/nprogress.js"></script>
	
		<script src="/js/IndexTable.js?t=<?= filemtime(WWW_ROOT.'/js/IndexTable.js') ?>"></script>
		
		<?php if (DEBUG || file_exists(WWW_ROOT.'/css/less/style.css') == false) : ?>
		<link href="/css/less/base.less?v=<?= filemtime(WWW_ROOT.'/css/less/base.less') ?>" rel="stylesheet/less" type="text/css" />
		<script>less = { env: 'development'};</script>
		<script src="/lib/less/dist/less.js" type="text/javascript"></script>
		<?php else : ?>
		<link href="/css/less/style.css?v=<?= filemtime(WWW_ROOT.'/css/less/style.css') ?>" rel="stylesheet" type="text/css" />
		<?php endif; ?>
		
		<script src="/js/script.js?t=<?= filemtime(WWW_ROOT.'/js/script.js') ?>" type="text/javascript"></script>
		<script src="/js/admin/script.js?t=<?= filemtime(WWW_ROOT.'/js/admin/script.js') ?>" type="text/javascript"></script>
		
		<link href="<?= BASE_HREF ?>lib/fontawesome-free-5.15.3-web/css/v4-shims.min.css" rel="stylesheet" type="text/css" />
		<link href="<?= BASE_HREF ?>lib/fontawesome-free-5.15.3-web/css/all.min.css" rel="stylesheet" type="text/css" />
	</head>
<body class="<?= isset($body_class) ? $body_class : '' ?>">
    <header>
        <div class="notifications-bar">
            <div class="notifications-right">
                <span class="current-user"> </span>
                <a href="<?= appUrl('/?m=admin&c=auth&a=logoff') ?>" class="fa fa-sign-out"></a>
            </div>
            <div class="administration-name"><a href="<?= appUrl('/') ?>">Admin: Toolbox</a></div>
        </div>
    </header>
	<div class="nav-side-menu">
		<div class="menu-mobile-spacer"></div>
	    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
	
		<div class="menu-list">

			<ul id="menu-content" class="menu-content collapse out">
    		<?php if (isset($menuItems)) foreach($menuItems as $mi) : ?>
				<li> 
					<a class="nav-link" href="<?= appUrl($mi->getUrl()) ?>"><i class="fa <?= $mi->getIcon() ?>"></i> <?= esc_html($mi->getLabel()) ?></a>
				</li>
    		<?php endforeach; ?>
			</ul>
		</div>
	</div>

	<div class="main-content">
		<?php output_user_messages() ?>
		
		<?php print $content ?>
	</div>


</body>
</html>
