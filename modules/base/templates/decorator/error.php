<!doctype html>
<html lang="nl">
	<head>
		<meta charset="utf-8">
		<title>Toolbox</title>

		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

		<script src="<?= BASE_HREF ?>js/jquery-3.3.1.min.js" type="text/javascript"></script>
		<script src="<?= BASE_HREF ?>js/bootstrap.min.js" type="text/javascript"></script>

		<script src="<?= BASE_HREF ?>lib/moment/moment-with-locales.min.js"></script>
		<script src="<?= BASE_HREF ?>lib/eonasdan-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
		
	<script src="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.min.js"></script>
	<script src="<?= BASE_HREF ?>lib/jquery.cookie.js"></script>
	<script src="<?= BASE_HREF ?>js/jquery.horizontalsplitcontainer.js"></script>
	<link href="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.structure.min.css" rel="stylesheet" type="text/css" />
	<link href="<?= BASE_HREF ?>lib/jquery-ui/jquery-ui.theme.css" rel="stylesheet" type="text/css" />
	
	<script src="<?= BASE_HREF ?>lib/select2/js/select2.min.js"></script>
	<link href="<?= BASE_HREF ?>lib/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
		
		<script src="<?= BASE_HREF ?>js/IndexTable.js?t=<?= filemtime(WWW_ROOT.'/js/IndexTable.js') ?>"></script>
		<script src="<?= BASE_HREF ?>js/forms/form-actions.js?t=<?= filemtime(WWW_ROOT.'/js/forms/form-actions.js') ?>" type="text/javascript"></script>
		
		
		<?php if (DEBUG || file_exists(WWW_ROOT.'/css/less/style.css') == false) : ?>
		<link href="<?= BASE_HREF ?>css/less/base.less?v=<?= filemtime(WWW_ROOT.'/css/less/base.less') ?>" rel="stylesheet/less" type="text/css" />
		<script>less = { env: 'development'};</script>
		<script src="<?= BASE_HREF ?>lib/less/dist/less.js" type="text/javascript"></script>
		<?php else : ?>
		<link href="<?= BASE_HREF ?>css/less/style.css?v=<?= filemtime(WWW_ROOT.'/css/less/style.css') ?>" rel="stylesheet" type="text/css" />
		<?php endif; ?>
		
		<script src="<?= BASE_HREF ?>js/script.js?t=<?= filemtime(WWW_ROOT.'/js/script.js') ?>" type="text/javascript"></script>
		
		<link href="<?= BASE_HREF ?>lib/font-awesome-4/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
		
		<link href="<?= BASE_HREF ?>lib/tinymce/skins/lightgray/skin.min.css" rel="stylesheet" type="text/css" />
		<script src="<?= BASE_HREF ?>lib/tinymce/tinymce.min.js"></script>
		<script src="<?= BASE_HREF ?>lib/tinymce/jquery.tinymce.min.js"></script>
		
	</head>
<body class="<?= isset($body_class) ? $body_class : '' ?>">
    <header>
        <div class="notifications-bar">
            &nbsp;
        </div>
    </header>
	<div class="nav-side-menu">
		<div class="menu-mobile-spacer"></div>
	    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
	
	</div>

	<div class="main-content">

		<?php print $content ?>
		
	</div>


</body>
</html>
