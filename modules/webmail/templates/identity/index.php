
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=webmail&c=identity&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Identiteiten</h1>
</div>



<div id="identity-table-container"></div>




<script>

var t = new IndexTable('#identity-table-container');


t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=webmail&c=identity&a=edit&id=' + $(row).data('record').identity_id);
});

t.setSortUpdate(function(evt) {
	var ids = [];
	$('#identity-table-container tbody tr').each(function(index, node) {
		ids.push( $(node).data('record').identity_id );
	});

	$.ajax({
		url: appUrl('/?m=webmail&c=identity&a=sort'),
		type: 'POST',
		data: {
			ids: ids.join(',')
		}
	});
});

t.setConnectorUrl( '/?m=webmail&c=identity&a=search' );


// t.addColumn({
// 	fieldName: 'offer_status_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });
t.addColumn({
	fieldName: 'from_name',
	fieldDescription: 'Naam',
	fieldType: 'text'
});
t.addColumn({
	fieldName: 'from_email',
	fieldDescription: 'E-mail',
	fieldType: 'text'
});
t.addColumn({
	fieldName: 'active',
	fieldDescription: 'Actief',
	fieldType: 'boolean',
	searchable: false
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var identity_id = record['identity_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=webmail&c=identity&a=edit&id=' + identity_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=webmail&c=identity&a=delete&id=' + identity_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.from_name);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>