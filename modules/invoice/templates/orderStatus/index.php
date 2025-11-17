
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=invoice&c=orderStatus&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Order statussen</h1>
</div>



<div id="order-status-table-container"></div>




<script>

var t = new IndexTable('#order-status-table-container');


t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=invoice&c=orderStatus&a=edit&id=' + $(row).data('record').order_status_id);
});

t.setSortUpdate(function(evt) {
	var ids = [];
	$('#order-status-table-container tbody tr').each(function(index, node) {
		ids.push( $(node).data('record').order_status_id );
	});

	$.ajax({
		url: appUrl('/?m=invoice&c=orderStatus&a=sort'),
		type: 'POST',
		data: {
			ids: ids.join(',')
		}
	});
});

t.setConnectorUrl( '/?m=invoice&c=orderStatus&a=search' );


// t.addColumn({
// 	fieldName: 'order_status_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });
t.addColumn({
	fieldName: 'description',
	fieldDescription: 'Omschrijving',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'active',
	fieldDescription: 'Actief',
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
		var order_status_id = record['order_status_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=invoice&c=orderStatus&a=edit&id=' + order_status_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=invoice&c=orderStatus&a=delete&id=' + order_status_id));
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