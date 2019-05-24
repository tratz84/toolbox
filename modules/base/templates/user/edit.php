

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=user') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1><?= t('Add user') ?></h1>
    <?php else : ?>
    <h1><?= t('Edit user') ?></h1>
    <?php endif; ?>
</div>


<?php print $userForm->render() ?>

<br/><br/>

<script>

$(document).ready(function() {
	$('[name=user_type]').change(function() {
		autosetUserCapabilityContainer();
	});

	autosetUserCapabilityContainer();
});

function autosetUserCapabilityContainer() {
	if ($('[name=user_type]').val() == 'admin') {
		$('.widget-container-user-capabilities').hide();
		$('.baseforms-list-user-ip-line-widget').hide();
	} else {
		$('.widget-container-user-capabilities').show();
		$('.baseforms-list-user-ip-line-widget').show();
	}
}

</script>



