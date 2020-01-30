


<div id="payment-overview-table-container"></div>

<script>


var pot = new IndexTable('#payment-overview-table-container', {
	autoloadNext: true
});


pot.setConnectorUrl( '/?m=payment&c=paymentOverview&a=search&<?= http_build_query($params) ?>' );

pot.setRowClick(function(row) {
	var r = $(row).data('record');

	window.location = appUrl('/?m=payment&c=payment&id=' + r.payment_id);
});

pot.setCallbackRenderRows(function() {
	$(this.container).find('tbody tr').each(function(index, node) {
		var r = $(node).data('record');
		if (!r) return;

		if (r.cancelled == 1) {
			$(node).addClass('cancelled');
		}
	});
});


pot.addColumn({
	fieldName: 'payment_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});
pot.addColumn({
	fieldName: 'payment_date',
	fieldDescription: 'Betaaldatum',
	fieldType: 'date',
	searchable: false
});
pot.addColumn({
	fieldName: 'description',
	fieldDescription: 'Kenmerk',
	fieldType: 'text',
	searchable: false,
	render: function(row) {
		var pd = $.trim(row.payment_description);

		if (pd != '')
			return pd;

		return row.payment_line_description1;
	}
});
pot.addColumn({
	fieldName: 'payment_amount',
	fieldType: 'currency',
	fieldDescription: 'Bedrag',
	searchable: false
});

pot.addColumn({
	fieldName: 'cancelled',
	fieldType: 'boolean',
	fieldDescription: 'Geannuleerd',
	searchable: false
});


pot.load();

</script>


