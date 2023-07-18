
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>
	
	<h1><?= t('Mail server settings (out)')?></h1>
</div>


<?= $form->render() ?>

<div class="clear">
	<input type="button" value="Save + test" onclick="saveTest_Click();" />
</div>

<script>

$(document).ready(function() {
	toggleForm();
});

$('[name=server_type]').change(function() { toggleForm(); });

function toggleForm() {
	var server_type = $('[name=server_type]').val();

	var widgets = $('.widget-container-smtp-settings, .widget-container-azure-settings');
	widgets.hide();
	
	if (server_type == 'local') {
	} else if (server_type == 'smtp') {
		$('.widget-container-smtp-settings').show();
	} else if (server_type == 'azure') {
		$('.widget-container-azure-settings').show();
	}
}



function saveTest_Click() {
	showConfirmation('Send test',
		'E-mailadres: <input type="text" name="e" />',
		function() {
			var emailadres = $('[name=e]').val();
			if (validate_email(emailadres) == false) {
				alert('Invalid mailadres');
				return false;
			}

			$('[name=send_test]').val( emailadres );
			$('form.form-generator').submit();
		});
	
}



</script>


