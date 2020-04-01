
<div class="page-header">
	<div class="toolbox">
    	<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
    	<a href="javascript:void(0);" onclick="mailboxSearchSettingsSave_Click();" class="fa fa-save save-filters"></a>
	</div>
	
	<h1>Mailbox search settings</h1>
</div>


<?= $form->render() ?>



<script>

function mailboxSearchSettingsSave_Click() {
	var data = serialize2object('form.form-mailbox-search-settings-form');
	
	$.ajax({
		url: appUrl('/?m=webmail&c=mailbox/search&a=settings_save'),
		type: 'POST',
		data: data,
		success: function(data, xhr, textStatus) {
			if (data.error) {
				alert('Error: ' + data.message);
				return;
			}

			close_popup();

			$(window).trigger('webmail-reload');
		}
	});
	
}


</script>

