

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=invoice&c=invoice&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1><?= strOrder(3) ?></h1>
</div>


<?= $actionContainer->render() ?>


<div id="invoice-table-container"></div>




<script>

var t = new IndexTable('#invoice-table-container');

t.setRowClick(function(row, evt) {

	if ($(evt.target).hasClass('td-update-status') || $(evt.target).closest('.td-update-status').length > 0) {
		return;
	}

	window.location = appUrl('/?m=invoice&c=invoice&a=edit&id=' + $(row).data('record').invoice_id);
});

t.setConnectorUrl( '/?m=invoice&c=invoice&a=search' );


t.addColumn({
	fieldName: 'invoiceNumberText',
	fieldDescription: '<?= strOrder(1) ?> nr',
	fieldType: 'text',
	width: 120,
	searchable: true
});
t.addColumn({
	fieldName: 'customer_name',
	fieldDescription: 'Klant',
	fieldType: 'text',
	render: function(record) {
		return format_customername( record );
	},
	searchable: true
});
t.addColumn({
	fieldName: 'subject',
	fieldDescription: 'Omschrijving',
	fieldType: 'text',
	searchable: true
});

<?php if ($invoiceSettings->getPricesIncVat()) : ?>
t.addColumn({
	fieldName: 'total_calculated_price_incl_vat',
	fieldDescription: 'Bedrag',
	fieldType: 'currency',
	searchable: false
});
<?php else : ?>
t.addColumn({
	fieldName: 'total_calculated_price',
	fieldDescription: 'Bedrag',
	fieldType: 'currency',
	searchable: false
});
<?php endif; ?>

t.addColumn({
	fieldName: 'invoice_status_description',
	fieldDescription: 'Status',
	fieldType: 'select',
	filterOptions: <?= json_encode($invoiceStatus) ?>,
	searchable: true
});

t.addColumn({
	fieldName: 'invoice_date',
	fieldDescription: 'Factuurdatum',
	fieldType: 'date',
	searchable: false
});


t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var invoice_id = record['invoice_id'];

		var anchPrint = $('<a class="fa fa-print" />');
		anchPrint.attr('target', '_blank');
		anchPrint.attr('href', appUrl('/?m=invoice&c=invoice&a=print&id=' + invoice_id));
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=invoice&c=invoice&a=edit&id=' + invoice_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=invoice&c=invoice&a=delete&id=' + invoice_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.subject + ' voor ' + record.name);

		if (record['deletable'] == false) {
			anchDel.css('visibility', 'hidden');
		}

		
		var container = $('<div />');
		container.append(anchPrint);
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>


<script>


function bulkUpdateStatus(id, desc) {
	cbu.invoiceStatusId = id;
	cbu.description = desc;

	cbu.updateTable();
	
	close_popup();
}

function bulkUpdateStatusCancel() {
	cbu.invoiceStatusId = null;
	cbu.description = null;
	
	cbu.updateTable();

	close_popup();
}

function ContainerBulkUpdate() {
	this.invoiceStatusId = null;
	this.description = null;
	
	this.init = function() {
		var me = this;
		
        $('#btnChangeStatus').click(function() {
        	show_popup(appUrl('/?m=invoice&c=invoice&a=popup_status'));
        });

        t.setCallbackRenderDone( function() {
            me.updateTable();
        } );
	};

	this.updateStatus = function(trRow) {
		var r = $(trRow).data('record');
		let invoiceDescription = this.description;
		
		if (r.invoice_status_id == this.invoiceStatusId) {
			showAlert('Status niet gewijzigd', 'Huidige status gelijk aan nieuwe status');
			return;
		}

		$(trRow).find('.td-update-status button').prop('disabled', true);

		$.ajax({
			type: 'POST',
			url: appUrl('/?m=invoice&c=invoice&a=update_status'),
			data: {
				invoice_id: r.invoice_id,
				invoice_status_id: this.invoiceStatusId
			},
			success: function(data) {
				if (data.status == 'error') {
					showAlert('Error', data.message);
				} else {
					$(trRow).find('.td-invoice-status-description').text( invoiceDescription );
				}
			}
		});
	};

	this.updateTable = function() {
		var me = this;
		
		$('#invoice-table-container table').find('.th-invoice-status-update').remove();
		$('#invoice-table-container table').find('.td-update-status').remove();
		
		if (this.invoiceStatusId == null || this.description == null)
			return;
		
		$('#invoice-table-container table thead .th-invoice-status-description').after('<th class="th-invoice-status-update">Bijwerken</th>');
		
		$('#invoice-table-container table tbody tr').each(function(index, row) {
			// skip no-results record (duh :D)
			if ($(row).hasClass('no-results')) {
				return;
			}
			
			var record = $(row).data('record');
			
			var td = $('<td class="td-update-status"><button /></td>');
			td.find('button').text( me.description );
			td.find('button').click(function() {
				me.updateStatus( $(this).closest('tr') );
			});
			if (record.invoice_status_id == me.invoiceStatusId) {
				td.find('button').prop('disabled', true);
			}

			$(row).find('.td-invoice-status-description').after( td );
		});
	};
	
}


var cbu = new ContainerBulkUpdate();
cbu.init();





</script>



