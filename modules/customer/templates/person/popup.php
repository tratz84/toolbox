
<div class="page-header">

	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>
	
	
	<h1><?= t('Select person') ?></h1>
	
</div>


<?php 

$ftc = generate_tabs('customer', 'popup-selectPersion', null);

$ftc->addTab(t('Select person'), '<div class="popup-error-list-container"></div> <div id="person-table-container"></div>', 10, ['name' => 'popup-select-person']);

$ftc->addTab(t('New person'), '<div class="popup-error-list-container"></div>'.$personForm->render(), 20, ['name' => 'popup-add-person']);

$ftc->render();


?>



<script>

var it_person = new IndexTable('#person-table-container');

it_person.setRowClick(function(row, evt) {
	var record = $(row).data('record');

	var lefw = $('.customer-form-select-person-list-edit').get(0).lefw;

	lefw.addRecord(function(row) {
		$(row).find('.hidden-field-widget-person-id input').val( record.person_id );
		$(row).find('.widget-full-name input').val( record.fullname );
		$(row).find('.widget-full-name .value').text( record.fullname );
	});
	
	close_popup();
});

it_person.setConnectorUrl( '/?m=customer&c=person&a=search' );


it_person.addColumn({
	fieldName: 'lastname',
	fieldDescription: '<?= t('Name') ?>',
	fieldType: 'text',
	render: function(record) {console.log(record);
		var t = '';
		if (record.lastname)
			t += record.lastname;
		if (record.insert_lastname && record.insert_lastname.match(/\S+/)) {
			t += ', ' + record.insert_lastname;
		}
		
		return t;
	},
	searchable: true
});

it_person.addColumn({
	fieldName: 'firstname',
	fieldDescription: '<?= t('Firstname') ?>',
	fieldType: 'text',
	searchable: true
});

it_person.setCallbackRenderRows(function() {
	if (this.refreshOnReturnSet) {
		return;
	}

	$(this.container).find('[name=lastname], [name=firstname]').keypress(function(evt) {
		// enter? reload
		if (evt.keyCode == 13) {
			it_person.load();
			evt.preventDefault();
		}
	});

	
	this.refreshOnReturnSet = true;
});


it_person.load();

</script>


