<style type="text/css">

.widget-container-container-message { clear: both; }
.widget-container-container-enc-settings { margin-top: 15px; }

</style>

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=securemail&c=secureMessage') ?>" class="fa fa-chevron-circle-left"></a>
		<?php if ($isNew == false) : ?>
			<a href="javascript:void(0);" onclick="delete_sm_Click();" class="fa delete fa-trash"></a>
		<?php endif; ?>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>
	
	<?php if ($isNew) : ?>
		<h1><?= t('Create message') ?></h1>
	<?php else : ?>
		<h1><?= t('Edit message') ?></h1>
	<?php endif; ?>
</div>




<?= $form->render() ?>



<script>

$(document).ready(() => {
	$('select[name=ttl_type]').on('change', () => {
		ttl_type_Change();
	});

	ttl_type_Change();
});

function ttl_type_Change() {
	let ttl_type = $('select[name=ttl_type]').val();

	if (ttl_type == 'once') {
		$('.widget-ttl-expires-on').hide();
	}
	if (ttl_type == 'expires') {
		$('.widget-ttl-expires-on').show();
	}
}


function delete_sm_Click() {
	showConfirmation( t('Delete'), t('Are you sure to delete this message?'), () => {
		let url = '/?m=securemail&c=secureMessage&a=delete&sm_id='+$('input[name=secure_message_id]').val();
		window.location = appUrl( url );
	});
}




</script>

