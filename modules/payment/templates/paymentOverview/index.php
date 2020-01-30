
<div class="page-header">

	<div class="toolbox">
		<?php if (hasCapability('payment', 'import-payments')) : ?>
		<a href="<?= appUrl('/?m=payment&c=import') ?>" class="fa fa-download"></a>
		<?php endif; ?>
		
		<?php if (hasCapability('payment', 'edit-payments')) : ?>
		<a href="<?= appUrl('/?m=payment&c=payment') ?>" class="fa fa-plus"></a>
		<?php endif; ?>
		
	</div>

	<h1>Betalingsoverzicht</h1>
</div>


<div id="payment-table-container"></div>

<script>

function component_deletePayment_Click(payment_id) {
	showConfirmation('Betaling verwijderen', 'Weet u zeker dat u deze betaling wilt verwijderen?', function() {
		var l = window.location;
		var back_url = l.pathname + l.search;

		window.location = appUrl('/?m=payment&c=payment&a=delete&id=' + payment_id + '&back_url=' + encodeURIComponent(back_url));
	});
}

var pot = new IndexTable('#payment-table-container', {
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
	fieldName: 'name',
	fieldDescription: 'Naam',
	fieldType: 'text',
	searchable: false,
	render: function(row) {
		if (row.payment_line_sort == 0) {
			return format_customername(row);
		}
	}
});

pot.addColumn({
	fieldName: 'payment_line_description1',
	fieldDescription: 'Kenmerk',
	fieldType: 'text',
	searchable: false
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


