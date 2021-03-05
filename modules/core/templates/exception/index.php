<?php
use admin\model\ExceptionLog;
use core\exception\DatabaseException;
use core\exception\NotForLiveException;
use core\exception\ContextNotFoundException;

try {
    header("HTTP/1.0 500 Internal Server Error");
    
    if (is_a($ex, ContextNotFoundException::class)) {
        // don't save ContextNotFound-Exceptions
    } else {
        $ctx = \core\Context::getInstance();
        
        $el = new ExceptionLog();
        $el->setContextName($ctx->getContextName());
        if ($ctx->getUser())
            $el->setUserId($ctx->getUser()->getUserId());
        $el->setRequestUri(substr($_SERVER['REQUEST_URI'], 0, 255));
        $el->setMessage($ex->getMessage());
        
        $stacktrace = '';
        if (is_a($ex, DatabaseException::class)) {
            $stacktrace .= 'Query: '.$ex->getQuery() . "\n\n";
        }
        $stacktrace .= $ex->getFile() . ' ('.$ex->getLine().')' . "\n";
        $stacktrace .= $ex->getTraceAsString();
        $el->setStacktrace($stacktrace);
        $el->setParameters(var_export($_REQUEST, true));
        $el->save();
    }
} catch (\Exception $ex) { }



?>
<!doctype html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Toolbox - serious error</title>

		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="icon" type="image/x-icon" href="favicon.ico">

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
    	
    		<div style="max-width: 800px; margin: 50px auto;">
    			<?= t('An error has occured') ?>:
    			<?php if (is_a($ex, NotForLiveException::class) == false || DEBUG) : ?>
    			 <?= $ex->getMessage() ?>
    			<?php endif; ?>
    			<br/>
    			<br/>
    			<a href="<?= appUrl('/') ?>"><?= t('Click here to go back to the dashboard') ?></a>
    		</div>
    		
    		<?php if (DEBUG) : ?>
    		
			
    		<?php if (is_a($ex, DatabaseException::class)) : ?>
    		<div>Last query: <?= esc_html($ex->getQuery()) ?></div>
    		<?php endif; ?>

			<pre><?php print $ex->getFile() ?> (<?php print $ex->getLine()?>)

<?php print $ex->getTraceAsString() ?>
			</pre>
			<?php endif; ?>
    		
    		
    	</div>

	</body>
</html>

