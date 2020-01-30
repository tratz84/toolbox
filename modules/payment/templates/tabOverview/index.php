


<div id="payment-overview-table-container"></div>

<script>

function component_deletePayment_Click(payment_id) {
	showConfirmation('Betaling verwijderen', 'Weet u zeker dat u deze betaling wilt verwijderen?', function() {
		var l = window.location;
		var back_url = l.pathname + l.search;

		window.location = appUrl('/?m=payment&c=payment&a=delete&id=' + payment_id + '&back_url=' + encodeURIComponent(back_url));
	});
	
}


var pot = new IndexTable('#payment-overview-table-container', {
	autoloadNext: true
});


pot.setConnectorUrl( '/?m=payment&c=paymentOverview&a=search&<?= http_build_query($params) ?>' );


pot.addColumn({
	fieldName: 'payment_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});
pot.addColumn({
	fieldName: 'paymentTypeText',
	fieldDescription: 'Soort',
	fieldType: 'text',
	searchable: false
});
pot.addColumn({
	fieldName: 'description',
	fieldDescription: 'Omschrijving',
	fieldType: 'text',
	searchable: false
});
pot.addColumn({
	fieldName: 'amount',
	fieldType: 'currency',
	fieldDescription: 'Bedrag',
	searchable: false
});

pot.addColumn({
	fieldName: 'payment_date',
	fieldDescription: 'Betaaldatum',
	fieldType: 'date',
	searchable: false
});
pot.addColumn({
	fieldName: 'actions',
	render: function(row) {
		console.log(row);
		return '<a href="javascript:void(0);" onclick="component_deletePayment_Click('+row.payment_id+');"><span class="fa fa-close"></span></a>';
	}
});


pot.load();

</script>


