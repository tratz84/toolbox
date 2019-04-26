

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=invoice&c=offer&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Offertes</h1>
</div>




<div class="action-box">
	<span><a href="javascript:void(0);" id="btnChangeStatus">Status bijwerken</a></span>
</div>

<hr/>



<div id="offer-table-container"></div>




<script>

var t = new IndexTable('#offer-table-container');

t.setRowClick(function(row, evt) {

	if ($(evt.target).hasClass('td-update-status') || $(evt.target).closest('.td-update-status').length > 0) {
		return;
	}
	
	window.location = appUrl('/?m=invoice&c=offer&a=edit&id=' + $(row).data('record').offer_id);
});

t.setConnectorUrl( '/?m=invoice&c=offer&a=search' );


t.addColumn({
	fieldName: 'offerNumberText',
	fieldDescription: 'Offerte nr',
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
	fieldName: 'offer_status_description',
	fieldDescription: 'Status',
	fieldType: 'select',
	filterOptions: <?= json_encode($offerStatus) ?>,
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
		var offer_id = record['offer_id'];

		var anchPrint = $('<a class="fa fa-print" />');
		anchPrint.attr('target', '_blank');
		anchPrint.attr('href', appUrl('/?m=invoice&c=offer&a=print&id=' + offer_id));
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=invoice&c=offer&a=edit&id=' + offer_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=invoice&c=offer&a=delete&id=' + offer_id));
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


function bulkUpdateStatus(id, desc) {
	cbu.offerStatusId = id;
	cbu.description = desc;

	cbu.updateTable();
	
	close_popup();
}

function bulkUpdateStatusCancel() {
	cbu.offerStatusId = null;
	cbu.description = null;
	
	cbu.updateTable();

	close_popup();
}

function ContainerBulkUpdate() {
	this.offerStatusId = null;
	this.description = null;
	
	this.init = function() {
		var me = this;
		
        $('#btnChangeStatus').click(function() {
        	show_popup(appUrl('/?m=invoice&c=offer&a=popup_status'));
        });

        t.setCallbackRenderDone( function() {
            me.updateTable();
        } );
	};

	this.updateStatus = function(trRow) {
		var r = $(trRow).data('record');
		let offerDescription = this.description;
		
		if (r.offer_status_id == this.offerStatusId) {
			showAlert('Status niet gewijzigd', 'Huidige status gelijk aan nieuwe status');
			return;
		}

		$(trRow).find('.td-update-status button').prop('disabled', true);

		$.ajax({
			type: 'POST',
			url: appUrl('/?m=invoice&c=offer&a=update_status'),
			data: {
				offer_id: r.offer_id,
				offer_status_id: this.offerStatusId
			},
			success: function(data) {
				if (data.status == 'error') {
					showAlert('Error', data.message);
				} else {
					$(trRow).find('.td-offer-status-description').text( offerDescription );
				}
			}
		});
	};

	this.updateTable = function() {
		var me = this;
		
		$('#offer-table-container table').find('.th-offer-status-update').remove();
		$('#offer-table-container table').find('.td-update-status').remove();
		
		if (this.offerStatusId == null || this.description == null)
			return;
		
		$('#offer-table-container table thead .th-offer-status-description').after('<th class="th-offer-status-update">Bijwerken</th>');
		
		$('#offer-table-container table tbody tr').each(function(index, row) {
			var td = $('<td class="td-update-status"><button /></td>');
			td.find('button').text( me.description );
			td.find('button').click(function() {
				me.updateStatus( $(this).closest('tr') );
			});

			$(row).find('.td-offer-status-description').after( td );
		});
	};
}

var cbu = new ContainerBulkUpdate();
cbu.init();

</script>



