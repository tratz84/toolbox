
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=webmail&c=maintenance/index') ?>"
			class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('Purge folders')?></h1>
</div>


<?= $form->render() ?>

<div id="progress"></div>

<input type="button" id="btnPurge" onclick="purge_Click();" value="Purge" />


<script>

function purge_Click() {

	var cid = $('[name=connectorId]').val();

	if (cid == '') {
		showAlert('Error', 'No connector selected');
		return;
	}

	$('#btnPurge').prop('disabled', true);
	$('#progress').html('<img src="'+appSettings.base_href+'images/ajax-loader-big.gif" /> Purging folder.. this can take a while (minutes)');
	
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=webmail&c=maintenance/purgefolder'),
		data: {
			a: 'purge',
			connectorId: cid,
			folderName: $('[name=folderName]').val()
		},
		success: function(data, xhr, textStatus) {
			if (data.success) {
				showAlert(_('Folder purged'), data.message);

			} else {
				showAlert(_('Error'), data.message);
			}
			
			// reset form
			$('#btnPurge').prop('disabled', false);
			$('#progress').empty();
		}
	});
	
}




</script>



