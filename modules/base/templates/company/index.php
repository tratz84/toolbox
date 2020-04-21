
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=company&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Overzicht bedrijven</h1>
</div>



<div id="company-table-container"></div>




<script>

var t = new IndexTable('#company-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=base&c=company&a=edit&company_id=' + $(row).data('record').company_id);
});

t.setConnectorUrl( '/?m=base&c=company&a=search' );


// t.addColumn({
// 	fieldName: 'company_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });
t.addColumn({
	fieldName: 'company_name',
	fieldDescription: 'Bedrijfsnaam',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'contact_person',
	fieldDescription: 'Contactpersoon',
	fieldType: 'text',
	searchable: true
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var company_id = record['company_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=base&c=company&a=edit&company_id=' + company_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=base&c=company&a=delete&company_id=' + company_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.company_name);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>