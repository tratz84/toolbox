
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=customer&c=company&a=edit') ?>" onclick="$('#company-table-container [name=lastname]').focus();" class="fa fa-plus" target="_blank"></a>
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>
	
	
	<h1><?= t('Select company') ?></h1>
	
</div>


<div id="company-table-container"></div>




<script>

var it_company = new IndexTable('#company-table-container');

it_company.setRowClick(function(row, evt) {
	var record = $(row).data('record');

	var lefw = $('.customer-form-select-company-list-edit').get(0).lefw;

	lefw.addRecord(function(row) {
		$(row).find('.hidden-field-widget-company-id input').val( record.company_id );
		$(row).find('.widget-company-name input').val( record.company_name );
		$(row).find('.widget-company-name .value').text( record.company_name );
	});
	
	close_popup();
});

it_company.setConnectorUrl( '/?m=customer&c=company&a=search' );


it_company.addColumn({
	fieldName: 'company_name',
	fieldDescription: '<?= t('Name') ?>',
	fieldType: 'text',
	searchable: true
});

it_company.setCallbackRenderRows(function() {
	if (this.refreshOnReturnSet) {
		return;
	}

	$(this.container).find('[name=name]').keypress(function(evt) {
		// enter? reload
		if (evt.keyCode == 13) {
			it_company.load();
			evt.preventDefault();
		}
	});

	
	this.refreshOnReturnSet = true;
});


it_company.load();

</script>


