

	<div class="page-header">
		<div class="toolbox">
			<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
		</div>
		
		<h1>Foutmelding</h1>
	</div>
	

<div class="exception-container">

    
    <div class="item">
    	<label>Id</label>
        <div>
        	<?= esc_html($ex->getExceptionLogId()) ?>
        </div>
    </div>
    
    <div class="item">
    	<label>Created</label>
        <div>
        	<?= esc_html($ex->getCreated()) ?>
        </div>
    </div>
    
    <div class="item">
    	<label>Context name</label>
        <div>
        	<?= esc_html($ex->getContextName()) ?>
        </div>
    </div>
    
    <div class="item">
    	<label>User id</label>
        <div>
        	<?= esc_html($ex->getUserId()) ?>
        </div>
    </div>
    
    <div class="item">
    	<label>Url</label>
        <div>
        	<?= esc_html($ex->getRequestUri()) ?>
        </div>
    </div>
    
    <div class="item">
    	<label>Parameters</label>
        <div>
        	<pre><?= esc_html($ex->getParameters()) ?></pre>
        </div>
    </div>
    
    <div class="item">
    	<label>Exception message</label>
        <div>
        	<?= esc_html($ex->getMessage()) ?>
        </div>
    </div>
    
    <div class="item">
    	<label>Stacktrace</label>
        <div>
        	<pre><?= esc_html($ex->getStacktrace()) ?></pre>
        </div>
    </div>
    
</div>
