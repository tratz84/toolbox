
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=webmail&c=maintenance/index') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('Update action')?></h1>
</div>


<?= $form->render() ?>

<div id="progress"></div>

<input type="button" value="Update actions" id="btnUpdate" onclick="doUpdate();" />


<script>

function doUpdate() {

	$('#btnUpdate').prop('disabled', true);
	$('#progress').html('<img src="'+appSettings.base_href+'images/ajax-loader-big.gif" /> Updating action-states.. this can take a while (minutes)');

	
	var formData = serialize2object('.form-webmail-update-action-form');
	$.ajax({
		url: appUrl('/?m=webmail&c=maintenance/updateaction&a=update'),
		type: 'POST',
		data: formData,
		success: function(data, xhr, textStatus) {
			$('#btnUpdate').prop('disabled', false);
			$('#progress').empty();
					

			if (data.success) {
				showAlert('Success', data.message);
			}
			else {
				showAlert('Error', data.message);
			}
			
		}
	});
	
}



</script>
