
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=project&c=projectHourType&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
	<h1>Uursoort</h1>
</div>




<div id="hour-type-table-container"></div>




<script>

var t = new IndexTable('#hour-type-table-container');


t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=project&c=projectHourType&a=edit&id=' + $(row).data('record').project_hour_type_id);
});

t.setSortUpdate(function(evt) {
	var ids = [];
	$('#hour-type-table-container tbody tr').each(function(index, node) {
		ids.push( $(node).data('record').project_hour_type_id );
	});

	$.ajax({
		url: appUrl('/?m=project&c=projectHourType&a=sort'),
		type: 'POST',
		data: {
			ids: ids.join(',')
		}
	});
});


t.setConnectorUrl( '/?m=project&c=projectHourType&a=search' );


// t.addColumn({
// 	fieldName: 'offer_type_id',
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
	fieldName: 'default_selected',
	fieldDescription: 'Standaard gekozen',
	fieldType: 'boolean',
	searchable: false
});
t.addColumn({
	fieldName: 'visible',
	fieldDescription: 'Zichtbaar',
	fieldType: 'boolean',
	searchable: false
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var project_hour_type_id = record['project_hour_type_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=project&c=projectHourType&a=edit&id=' + project_hour_type_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=project&c=projectHourType&a=delete&id=' + project_hour_type_id));
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