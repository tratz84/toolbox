

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=invoice&c=tobill&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Te factureren</h1>
</div>


<div id="tobill-table-container"></div>




<script>


function toggleBilled(anchor, toBillId) {
	console.log( toBillId );

	$.ajax({
		type: 'POST',
		url: appUrl('/?m=invoice&c=tobill&a=toggle_billed&id=' + toBillId),
		success: function(data, xhr, textStatus) {
			console.log( anchor );
			$(anchor).closest('.td-billed').find('.state-text').text( data.billed?'Ja':'Nee' );
		}
	});
}



var t = new IndexTable('#tobill-table-container');

t.setRowClick(function(row, evt) {

	if ($(evt.target).hasClass('td-billed') || $(evt.target).closest('.td-billed').length) {
		return;
	}

	window.location = appUrl('/?m=invoice&c=tobill&a=edit&id=' + $(row).data('record').to_bill_id);
});

t.setConnectorUrl( '/?m=invoice&c=tobill&a=search' );

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
	fieldName: 'billed',
	fieldDescription: 'Gefactureerd',
	fieldType: 'select',
	filterOptions: [{ 'value':'', 'text': 'Gefactureerd'}, { 'value': '1', 'text': 'Ja' }, { 'value' : '0', 'text': 'Nee' } ],
	searchable: true,
	render: function(record) {
		var t = '';

		t += '<a href="javascript:void(0);" onclick="toggleBilled(this, '+record.to_bill_id+');" class="fa fa-repeat"></a> ';

		t += '<span class="state-text">' + (record.billed ? 'Ja' : 'Nee') + '</span>'

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





