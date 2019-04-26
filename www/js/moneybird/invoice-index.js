/**
 * 
 */



var it = $('#invoice-table-container').data('IndexTable');

setTimeout(function() {
	it.insertColumn({
	    fieldName: 'moneybird',
	    fieldDescription: 'Moneybird',
	    fieldType: 'text',
	    searchable: false,
	    render: function(rec) {
	        return 'hmz';
	    }
	}, 55);
	
	it.renderHeader();
	it.render();
	
	mb_update_export_buttons();
	
	it.setCallbackRenderDone(function() {
		mb_update_export_buttons();
	});
}, 0);


function mb_update_export_buttons() {
	
	var invoiceIds = [];
	
	$('#invoice-table-container table tbody tr').each(function(index, node) {
		$(node).find('.td-moneybird').text('');
		
		var rec = $(node).data('record');
		if (rec.invoice_id) {
			invoiceIds.push(rec.invoice_id);
		}
	});
	
	if (invoiceIds.length) {
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=moneybird&c=invoice&a=check_export_button'),
			data: {
				invoice_ids: invoiceIds
			},
			success: function(data, xhr, textStatus) {
				console.log( data );
				for(var invoiceId in data.invoices) {
					
					// lookup record
					var tr = null;
					$('#invoice-table-container table tbody tr').each(function(index, node) {
						if ($(node).data('record').invoice_id == invoiceId) {
							tr = node;
							return false;
						}
					});
					if (tr == null) continue;
					
					$(tr).find('.td-moneybird').data('click-disabled', true);
					
					var inv = data.invoices[invoiceId];
//					$(tr).find('.td-moneybird').text(inv['status']);
					if (inv['status'] == 'exported') {
						$(tr).find('.td-moneybird').append('Geëxporteerd');
					} else if (inv['status'] == 'to-export') {
						$(tr).find('.td-moneybird').append('<button onclick="mb_export_invoice(this);">Exporteer</button>');
					} else {
						
					}
				}
			}
		});
	}
}



function mb_export_invoice(objButton) {
	var tr = $(objButton).closest('tr');
	var record = $(tr).data('record');
	
	$(objButton).prop('disabled', true);
	
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=moneybird&c=invoice&a=ajx_export'),
		data: {
			invoice_id: record.invoice_id
		},
		success: function(data, xhr, textStatus) {
			if (data.success) {
				$(tr).find('.td-moneybird').text('Geëxporteerd');
			} else {
				showAlert('Error', 'Error bij het exporteren van factuur '+$(tr).find('.td-invoicenumbertext').text()+': ' + data.message);
				$(objButton).prop('disabled', false);
			}
		},
		error: function(err) {
			alert('Fout opgetreden: ' + err);
		}
	});
	
}



