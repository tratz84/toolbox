

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=invoice&c=tobill&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Te factureren</h1>
</div>


<div id="tobill-table-container"></div>




<script>


function togglePaid(anchor, toBillId) {
	console.log( toBillId );

	$.ajax({
		type: 'POST',
		url: appUrl('/?m=invoice&c=tobill&a=toggle_paid&id=' + toBillId),
		success: function(data, xhr, textStatus) {
			console.log( anchor );
			$(anchor).closest('.td-paid').find('.state-text').text( data.paid?'Ja':'Nee' );
		}
	});
}



var t = new IndexTable('#tobill-table-container');

t.setRowClick(function(row, evt) {

	if ($(evt.target).hasClass('td-paid') || $(evt.target).closest('.td-paid').length) {
		return;
	}

	window.location = appUrl('/?m=invoice&c=tobill&a=edit&id=' + $(row).data('record').to_bill_id);
});

t.setConnectorUrl( '/?m=invoice&c=tobill&a=search' );

t.addColumn({
	fieldName: 'type',
	fieldDescription: toolbox_t('Type'),
	fieldType: 'select',
	filterOptions: [{ 'value':'', 'text': 'Type'}, { 'value': 'debet', 'text': toolbox_t('Debet') }, { 'value' : 'credit', 'text': toolbox_t('Credit') } ],
	searchable: true,
	render: function(row) {
		if (row.type == 'credit') {
			return toolbox_t('Credit');
		} else if (row.type == 'debet') {
			return toolbox_t('Debet');
		}
		return '';
	}
});
t.addColumn({
	fieldName: 'customer_name',
	fieldDescription: 'Klant',
	fieldType: 'text',
	render: function(record) {
		return format_customername(record);
	},
	searchable: true
});
t.addColumn({
	fieldName: 'short_description',
	fieldDescription: 'Omschrijving',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'amount',
	fieldDescription: 'Aantal',
	fieldType: 'text'
});
t.addColumn({
	fieldName: 'price',
	fieldDescription: 'Bedrag',
	fieldType: 'currency'
});

t.addColumn({
	fieldName: 'price',
	fieldDescription: 'Totaalbedrag',
	fieldType: 'currency',
	render: function(record) {
		return format_price( record.price * record.amount, true, {thousands: '.'} );
	}
});

t.addColumn({
	fieldName: 'paid',
	fieldDescription: toolbox_t('Paid'),
	fieldType: 'select',
	defaultValue: <?= json_encode($defaultValuePaid) ?>,
	filterOptions: [{ 'value':'', 'text': 'Paid'}, { 'value': '1', 'text': 'Ja' }, { 'value' : '0', 'text': 'Nee' } ],
	searchable: true,
	render: function(record) {
		var t = '';

		t += '<a href="javascript:void(0);" onclick="togglePaid(this, '+record.to_bill_id+');" class="fa fa-repeat"></a> ';

		t += '<span class="state-text">' + (record.paid ? 'Ja' : 'Nee') + '</span>'

		return t;
	}
});

t.addColumn({
	fieldName: 'created',
	fieldDescription: 'Aangemaakt op',
	fieldType: 'datetime',
	searchable: false
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var to_bill_id = record['to_bill_id'];

		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=invoice&c=tobill&a=edit&id=' + to_bill_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=invoice&c=tobill&a=delete&id=' + to_bill_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.subject);

		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>





