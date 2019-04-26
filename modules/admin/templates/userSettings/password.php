

<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<h1>Wachtwoord aanpassen</h1>
</div>

<form method="post" action="" class="form-generator">
    <div class="widget text-field-widget">
    	<label>Wachtwoord</label>
    	<input type="password" autocomplete="new-password" name="p1" />
    </div>
    
    
    <div class="widget text-field-widget">
    	<label>Bevestiging wachtwoord</label>
    	<input type="password" autocomplete="new-password" name="p2" />
    </div>
</form>