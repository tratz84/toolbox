

<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" onclick="$('form').submit()" class="fa fa-save"></a>
	</div>

	<h1>SignRequest instellingen</h1>

</div>


<div class="form-generator">
    <form method="post" action="">
    	<div class="widget text-field-widget">
        	<label>
        		SignRequest API Token
        		<?= infopopup('Te vinden op <a href="https://signrequest.com" target="_blank">SignRequest.com</a> =&gt; Instellingen =&gt; Teams =&gt; API Tokens =&gt; \'show\'') ?>
        	</label>
        	<input type="text" name="signrequestToken" value="<?= @esc_attr($settings['signrequestToken']) ?>" style="width: 400px;" />
        </div>

		<br/>

    	<div class="widget text-field-widget">
        	<label>
        		Template
        	</label>
        	Standaard template voor SignRequests kan worden toegevoegd onder: Stamgegevens =&gt; E-mail =&gt; Templates =&gt; Template met code: <a href="<?= appUrl('/?m=webmail&c=template&a=createOrEdit&code=SIGNREQUEST_TEMPLATE') ?>">SIGNREQUEST_TEMPLATE</a>
        </div>

    </form>
</div>
