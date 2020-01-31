


<div id="payment-overview-table-container"></div>

<script>


var pot = new IndexTable('#payment-overview-table-container', {
	autoloadNext: true
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


pot.setConnectorUrl( '/?m=payment&c=paymentOverview&a=search&<?= isset($params)?http_build_query($params):'' ?>' );

pot.setRowClick(function(tr, evt) {
	var r = $(tr).data('record');

	window.location = appUrl('/?m=payment&c=payment&id='+r.payment_id);
});


pot.addColumn({
	fieldName: 'payment_id',
	width: 40,
	fieldDescription: 'Id ',
	fieldType: 'text',
	searchable: false,
	render: function(row) {
		if (row.payment_line_sort == 0) {
			return row.payment_id;
		}
	}
});

pot.addColumn({
	fieldName: 'payment_line_sort',
	fieldDescription: 'Volgnr',
	fieldType: 'text',
	searchable: false,
	render: function(row) {
		return parseInt(row.payment_line_sort)+1;
	}
});

pot.addColumn({
	fieldName: 'payment_date',
	fieldDescription: 'Betaaldatum',
	fieldType: 'date',
	searchable: false,
	render: function(row) {
		if (row.payment_line_sort == 0) {
			dt = str2date(row.payment_date);
			return format_date(dt, {dmy: true});
		}
	}
});

pot.addColumn({
	fieldName: 'payment_line_description1',
	fieldDescription: 'Kenmerk',
	fieldType: 'text',
	searchable: false,
	render: function(row) {
		var d = $.trim(row.payment_line_description1);
		
		if (row.payment_line_sort == 0) {
			var pd = $.trim(row.payment_description);
			if (pd != '')
				d = pd + '<br/>' + d;
		}

		return d;
	}
});

pot.addColumn({
	fieldName: 'payment_line_amount',
	fieldDescription: 'Bedrag',
	fieldType: 'currency',
	searchable: false
});

pot.addColumn({
	fieldName: 'payment_method_description',
	fieldDescription: 'Methode',
	fieldType: 'text',
	searchable: false
});

pot.addColumn({
	fieldName: 'cancelled',
	fieldDescription: 'Geannuleerd',
	fieldType: 'boolean',
	searchable: false
});

pot.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function(row) {
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=payment&c=payment&id=' + row.payment_id));
		
		var container = $('<div />');
		container.append(anchEdit);

		return container;
// 		console.log(row);
	}
});


pot.load();

</script>


