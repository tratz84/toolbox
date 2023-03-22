

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=user') ?>" class="fa fa-chevron-circle-left"></a>
		<?php if ($isNew == false) : ?>
		<a href="<?= appUrl('/?m=base&c=user&a=delete&user_id='.$form->getWidgetValue('user_id')) ?>" class="fa fa-trash delete"></a>
		<?php endif; ?>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1><?= t('Add user') ?></h1>
    <?php else : ?>
    <h1><?= t('Edit user') ?></h1>
    <?php endif; ?>
</div>


<?php print $form->render() ?>

<br/><br/>

<script>

$(document).ready(function() {
	$('[name=user_type]').change(function() {
		autosetUserCapabilityContainer();
	});

	autosetUserCapabilityContainer();

	handle_deleteConfirmation();
});

function autosetUserCapabilityContainer() {
	if ($('[name=user_type]').val() == 'admin') {
		$('.widget-container-user-capabilities').hide();
		$('.widget-container-ips').hide();
	} else if ($('[name=user_type]').val() == 'user') {
		$('.widget-container-user-capabilities').show();
		$('.widget-container-ips').show();
		$('.widget-container-user-capabilities div.user-capability').show();
	} else {
		var type = $('[name=user_type]').val();
		
		$('.widget-container-user-capabilities').show();
		$('.widget-container-ips').show();

		$('.widget-container-user-capabilities div.user-capability').hide();
		$('.widget-container-user-capabilities div.user-capability.'+type).show();
	}
}

</script>



