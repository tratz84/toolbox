

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=invoice&c=order&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Orders</h1>
</div>




<div class="action-box">
	<span><a href="javascript:void(0);" id="btnChangeStatus">Status bijwerken</a></span>
</div>

<hr/>



<div id="order-table-container"></div>




<script>

var t = new IndexTable('#order-table-container');

t.setRowClick(function(row, evt) {

	if ($(evt.target).hasClass('td-update-status') || $(evt.target).closest('.td-update-status').length > 0) {
		return;
	}
	
	window.location = appUrl('/?m=invoice&c=order&a=edit&id=' + $(row).data('record').order_id);
});

t.setConnectorUrl( '/?m=invoice&c=order&a=search' );


t.addColumn({
	fieldName: 'orderNumberText',
	fieldDescription: 'Order nr',
	fieldType: 'text',
	width: 120,
	searchable: true
});
t.addColumn({
	fieldName: 'customer_name',
	fieldDescription: 'Klant',
	fieldType: 'text',
	render: function(record) {
		if (is_numeric(record.company_id) && parseInt(record.company_id) != 0) {
			record.name = record.company_name;
		}
		if (is_numeric(record.person_id) && parseInt(record.person_id) != 0) {
			record.name = record.lastname + ', ' + record.insert_lastname + ' ' + record.firstname;
		}

		return record.name;
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
	fieldName: 'order_status_description',
	fieldDescription: 'Status',
	fieldType: 'select',
	filterOptions: <?= json_encode($orderStatus) ?>,
	searchable: true
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
		var order_id = record['order_id'];

		var anchPrint = $('<a class="fa fa-print" />');
		anchPrint.attr('target', '_blank');
		anchPrint.attr('href', appUrl('/?m=invoice&c=order&a=print&id=' + order_id));
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=invoice&c=order&a=edit&id=' + order_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=invoice&c=order&a=delete&id=' + order_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.subject + ' voor ' + record.name);

		
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


function order_bulkUpdateStatus(id, desc) {
	cbu.orderStatusId = id;
	cbu.description = desc;

	cbu.updateTable();
	
	close_popup();
}

function order_bulkUpdateStatusCancel() {
	cbu.orderStatusId = null;
	cbu.description = null;
	
	cbu.updateTable();

	close_popup();
}

function ContainerBulkUpdate() {
	this.orderStatusId = null;
	this.description = null;
	
	this.init = function() {
		var me = this;
		
        $('#btnChangeStatus').click(function() {
        	show_popup(appUrl('/?m=invoice&c=order&a=popup_status'));
        });

        t.setCallbackRenderDone( function() {
            me.updateTable();
        } );
	};

	this.updateStatus = function(trRow) {
		var r = $(trRow).data('record');
		let orderDescription = this.description;
		
		if (r.order_status_id == this.orderStatusId) {
			showAlert('Status niet gewijzigd', 'Huidige status gelijk aan nieuwe status');
			return;
		}

		$(trRow).find('.td-update-status button').prop('disabled', true);

		$.ajax({
			type: 'POST',
			url: appUrl('/?m=invoice&c=order&a=update_status'),
			data: {
				order_id: r.order_id,
				order_status_id: this.orderStatusId
			},
			success: function(data) {
				if (data.status == 'error') {
					showAlert('Error', data.message);
				} else {
					$(trRow).find('.td-order-status-description').text( orderDescription );
				}
			}
		});
	};

	this.updateTable = function() {
		var me = this;
		
		$('#order-table-container table').find('.th-order-status-update').remove();
		$('#order-table-container table').find('.td-update-status').remove();
		
		if (this.orderStatusId == null || this.description == null)
			return;
		
		$('#order-table-container table thead .th-order-status-description').after('<th class="th-order-status-update">Bijwerken</th>');
		
		$('#order-table-container table tbody tr').each(function(index, row) {
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
			if (record.order_status_id == me.orderStatusId) {
				td.find('button').prop('disabled', true);
			}

			$(row).find('.td-order-status-description').after( td );
		});
	};
}

var cbu = new ContainerBulkUpdate();
cbu.init();

</script>



