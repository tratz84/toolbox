
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=webmail&c=externalTokens') ?>" class="fa fa-chevron-circle-left"></a>
		<?php if (!$isNew) : ?>
		<a href="<?= appUrl('/?m=webmail&c=externalTokens&a=delete&id='.$form->getWidgetValue('webmail_azure_token_id')) ?>" class="fa fa-trash delete"></a>
		<?php endif; ?>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1><?= t('Add external token') ?></h1>
    <?php else : ?>
    <h1><?= t('Edit external token') ?></h1>
    <?php endif; ?>
</div>


<?php print $form->render() ?>

<div style="margin-top: 25px;">
	<input id="btnRequestToken" type="button" value="<?= t('Request token') ?>" />
</div>


<script>

$(document).ready(function() {
	handle_deleteConfirmation();
	
	$('#btnRequestToken').on('click', function() {
		$('[name=request_token]').val( 1 );
		
		$('.form-azure-token-form').submit();
	});
});


</script>

