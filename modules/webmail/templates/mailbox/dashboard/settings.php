
<style type="text/css">

.widget-container-invoice-statuses { margin-bottom: 20px; }
.widget-container-invoice-statuses .html-field-widget { font-weight: bold; }
.widget-container-invoice-statuses .checkbox-field-widget { margin-left: 20px;; }

.form-recent-invoice-widget-form .submit-container { display: none; }

</style>

<div class="page-header">
	
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
		<a href="javascript:void(0);" onclick="saveWebmailDashboard_settings();" class="fa fa fa-save"></a>
	</div>
	
	<h1><?= t('Webmail settings') ?></h1>
	
</div>


<?= $form->render() ?>

<script>

function saveWebmailDashboard_settings() {
	var data = $('.form-mailbox-search-settings-form').serialize();

	$.ajax({
		url: appUrl('/?m=webmail&c=mailbox/dashboard&a=settings_save'),
		type: 'POST',
		data: data,
		success: function(data, xhr, textStatus) {
			
			close_popup();

			dash.loadWidget('webmail-archive');
		},
		error: function(xhr, textStatus, err) {
			showAlert('Error', 'Error: ' + xhr.responseText);
		}
	});
	
}


</script>

