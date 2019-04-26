
<style type="text/css">

.widget-container-invoice-statuses { margin-bottom: 20px; }
.widget-container-invoice-statuses .html-field-widget { font-weight: bold; }
.widget-container-invoice-statuses .checkbox-field-widget { margin-left: 20px;; }

.form-recent-invoice-widget-form .submit-container { display: none; }

</style>

<div class="page-header">
	
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
		<a href="javascript:void(0);" onclick="save_lastChangedInvoices_settings();" class="fa fa fa-save"></a>
	</div>
	
	<h1>Recente <?= strtolower(strOrder(1))?>instellingen</h1>
	
</div>


<?= $form->render() ?>

<script>

function save_lastChangedInvoices_settings() {
	var data = $('.form-recent-invoice-widget-form').serialize();

	$.ajax({
		url: appUrl('/?m=invoice&c=dashboardWidgets&a=lastInvoices_settings'),
		type: 'POST',
		data: data,
		success: function(data, xhr, textStatus) {
			
			close_popup();

			dash.loadWidget('invoice-recent-invoices');
		},
		error: function(xhr, textStatus, err) {
			showAlert('Error', 'Error: ' + xhr.responseText);
		}
	});
	
}


</script>

