<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Toolbox - admin</title>

		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/x-icon" href="favicon.ico">

		<script src="/js/jquery-3.3.1.min.js" type="text/javascript"></script>
		<script src="/js/bootstrap.min.js" type="text/javascript"></script>
		<script src="/js/script.js" type="text/javascript"></script>
		
		<?php if (DEBUG || file_exists(WWW_ROOT.'/css/less/style.css') == false) : ?>
		<link href="/css/less/base.less?v=<?= filemtime(WWW_ROOT.'/css/less/base.less') ?>" rel="stylesheet/less" type="text/css" />
		<script>less = { env: 'development'};</script>
		<script src="/lib/less/dist/less.js" type="text/javascript"></script>
		<?php else : ?>
		<link href="/css/less/style.css?v=<?= filemtime(WWW_ROOT.'/css/less/style.css') ?>" rel="stylesheet" type="text/css" />
		<?php endif; ?>
		
		
		<link href="/lib/font-awesome-4/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
	</head>
<body class="auth">

	<div class="main-content">
		<?php print $content ?>
	</div>

</body>
</html>
