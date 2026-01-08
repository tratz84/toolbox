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



<?php if ($isNew == false) : ?>
<hr/>
<table class="list-response-table">
	<thead>
		<tr>
			<th><?= t('Message') ?></th>
			<th><?= t('Ip') ?></th>
			<th><?= t('Opened') ?></th>
			<th><?= t('Created') ?></th>
		</tr>
	</thead>
	<tbody>
		<?php if (count($smlogs) == 0) : ?>
			<tr>
				<td colspan="100%" class="no-results-found"><?= t('No results found') ?></td>
			</tr>
		<?php else : ?>
			<?php foreach($smlogs as $sml) : ?>
			<tr>
				<td><?= esc_html($sml->getLogMessage()) ?></td>
				<td><?= esc_html($sml->getIp()) ?></td>
				<td><?= $sml->getOpened() ? t('Yes') : t('No') ?></td>
				<td><?= format_datetime($sml->getCreated()) ?></td>
			</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>

</table>
<?php endif; ?>



<script>

$(document).ready(() => {
	$('select[name=ttl_type]').on('change', () => {
		ttl_type_Change();
	});

	ttl_type_Change();

	$('input[name=link_to_message]').attr('readonly', 'readonly');
	let btnCopyLink = $('<a href="javascript:void(0);" class="fa fa-copy textfield-copy"></a>');
	btnCopyLink.on('click', function() {
		copyToClipboard( $('input[name=link_to_message]') );
	});
	btnCopyLink.insertAfter('input[name=link_to_message]');
});

function ttl_type_Change() {
	let ttl_type = $('select[name=ttl_type]').val();

	if (ttl_type == 'once') {
		$('#view-method-click2watch').prop('checked', true);
		$('input[name=view_method]').attr('disabled', 'disabled');
	}
	if (ttl_type == 'expires') {
		$('input[name=view_method]').removeAttr('disabled', '');
	}
}


let securemessage_is_deleted = <?= json_encode($sm->isDeleted() ? true : false) ?>;

function delete_sm_Click() {
	showConfirmation( t('Delete'), t('Are you sure to delete this message?'), () => {
		let url = '/?m=securemail&c=secureMessage&a=delete&sm_id='+$('input[name=secure_message_id]').val();
		window.location = appUrl( url );
	});

	let btnPurge = $('<button class="btn btn-danger">Purge</button>');
	btnPurge.on('click', () => {
		let url = '/?m=securemail&c=secureMessage&a=purge&sm_id='+$('input[name=secure_message_id]').val();
		window.location = appUrl( url );
	});
	btnPurge.insertAfter('.confirmation-dialog .modal-footer .btn-default');

	if ( securemessage_is_deleted ) {
		$('.confirmation-dialog .modal-footer .btn-primary').remove();

		$('.confirmation-dialog .modal-footer .btn-danger').focus();
	}
	
}




</script>

