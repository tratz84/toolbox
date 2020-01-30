
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=payment&c=paymentMethod&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Betalingsmogelijkheden</h1>
</div>



<div id="payment-method-table-container"></div>




<script>

var t = new IndexTable('#payment-method-table-container');


t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=payment&c=paymentMethod&a=edit&id=' + $(row).data('record').payment_method_id);
});

t.setSortUpdate(function(evt) {
	var ids = [];
	$('#payment-method-table-container tbody tr').each(function(index, node) {
		ids.push( $(node).data('record').payment_method_id );
	});

	$.ajax({
		url: appUrl('/?m=payment&c=paymentMethod&a=sort'),
		type: 'POST',
		data: {
			ids: ids.join(',')
		}
	});
});

t.setConnectorUrl( '/?m=payment&c=paymentMethod&a=search' );


// t.addColumn({
// 	fieldName: 'payment_method_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });

t.addColumn({
	fieldName: 'code',
	fieldDescription: 'Code',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'description',
	fieldDescription: 'Omschrijving',
	fieldType: 'text',
	searchable: false
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
		var payment_method_id = record['payment_method_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=payment&c=paymentMethod&a=edit&id=' + payment_method_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=payment&c=paymentMethod&a=delete&id=' + payment_method_id));
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