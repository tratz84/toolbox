

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=invoice&c=vat&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Btw tarieven</h1>
</div>





<div id="vat-table-container"></div>





<div id="offer-status-table-container"></div>




<script>

var t = new IndexTable('#offer-status-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=invoice&c=vat&a=edit&id=' + $(row).data('record').vat_id);
});

t.setConnectorUrl( '/?m=invoice&c=vat&a=search' );

t.setSortUpdate(function() {
	var ids = [];
	$('#offer-status-table-container tbody tr').each(function(index, node) {
		ids.push( $(node).data('record').vat_id );
	});

	$.ajax({
		url: appUrl('/?m=invoice&c=vat&a=sort'),
		type: 'POST',
		data: {
			ids: ids.join(',')
		}
	});
});


// t.addColumn({
// 	fieldName: 'vat_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });
t.addColumn({
	fieldName: 'description',
	fieldDescription: 'Omschrijving',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'percentage',
	fieldDescription: 'Percentage',
	fieldType: 'percentage',
	searchable: false
});
t.addColumn({
	fieldName: 'visible',
	fieldDescription: 'Zichtbaar',
	fieldType: 'boolean',
	searchable: false
});
t.addColumn({
	fieldName: 'default_selected',
	fieldDescription: 'Standaard gekozen',
	fieldType: 'boolean',
	searchable: false
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var vat_id = record['vat_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=invoice&c=vat&a=edit&id=' + vat_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=invoice&c=vat&a=delete&id=' + vat_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.description);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>