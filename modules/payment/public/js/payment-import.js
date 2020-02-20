/**
 * 
 */




function PaymentImportTable(container, opts) {
	
	this.container = container;
	this.opts = opts || { };
	this.data = [];
	this.table = null;
	
	this.setData = function(data) { this.data = data; };
	
	
	this.createTable = function() {
		this.table = $('<table class="list-response-table payment-import-table" />');
		this.createHead( this.table );
		
		
		
		$(this.table).append('<tbody class="tbody-lines" />');
		
		for(var i in this.data) {
			this.updateLine( this.data[i] );
			
		}
		$(this.container).append( this.table );
	};
	
	this.updateLine = function(line) {
		var tr = $('tr.import-line-'+line['payment_import_line_id']);
		var newRow = tr.length == 0 ? true : false;
		
		if (newRow) {
			var tr = $('<tr />');
			tr.addClass('import-line-' + line['payment_import_line_id']);
			tr.append('<td class="td-import-status" title="pil-id '+line['payment_import_line_id']+'" />');
			tr.append('<td class="td-customer" />');
			tr.append('<td class="td-invoice" />');
			tr.append('<td class="td-bankaccounts" />');
			tr.append('<td class="td-amount" />');
			tr.append('<td class="td-name" />');
			tr.append('<td class="td-description" />');
			
			if (this.opts.payment_import_status != 'done') {
				tr.append('<td class="td-action-buttons" />');
			}
		}
	
		tr.data('pil', line);
		tr.find('.td-import-status').text( line['import_status'] );
		
		if (newRow) {
			var anchViewCompany = $('<a href="javascript:void(0);" class="fa fa-search view-customer"></a>');
			anchViewCompany.click(function() {
				var tr = $(this).closest('tr');
				var pil = tr.data('pil');
				if (pil['company_id']) {
					window.open(appUrl('/?m=base&c=company&a=edit&company_id='+pil['company_id']), '_blank');
				}
				if (pil['person_id']) {
					window.open(appUrl('/?m=base&c=person&a=edit&person_id='+pil['person_id']), '_blank');
				}
			});
			
			tr.find('.td-customer').append( anchViewCompany );
			tr.find('.td-customer').append('<div class="customer-selection" />');
			tr.find('.td-customer .customer-selection').click(function(evt) {
				this.customer_selection_Click( evt.target );
			}.bind(this));
		}
//		tr.find('.td-customer .customer-selection').empty();
//		console.log(name);
		tr.find('.td-customer .customer-selection').text( line['customer_name'] );
		if (line['company_id'] || line['person_id']) {
			tr.find('.td-customer a.view-customer').show();
		} else {
			tr.find('.td-customer a.view-customer').hide();
		}
		
		if (newRow) {
			var anchViewInvoice = $('<a href="javascript:void(0);" class="fa fa-search view-invoice"></a>');
			anchViewInvoice.click(function() {
				var tr = $(this).closest('tr');
				var pil = tr.data('pil');
				if (pil['invoice_id']) {
					window.open(appUrl('/?m=invoice&c=invoice&a=edit&id='+pil['invoice_id']), '_blank');
				}
			});

			tr.find('.td-invoice').append( anchViewInvoice );
			tr.find('.td-invoice').append('<div class="invoice-selection" />');
			tr.find('.td-invoice .invoice-selection').click(function(evt) {
				this.invoice_selection_Click( evt.target );
			}.bind(this));
		}
		tr.find('.td-invoice .invoice-selection').text( line['invoice_number'] );
		if (line['invoice_id']) {
			tr.find('.td-invoice .view-invoice').show();
		} else {
			tr.find('.td-invoice .view-invoice').hide();
		}
		
		tr.find('.td-bankaccounts').html('<div>'+line['bankaccountno']+'</div><div>'+line['bankaccountno_contra']+'</div>');
		tr.find('.td-amount').text( format_price(line['amount']) );
		tr.find('.td-name').text( line['name'] );
		tr.find('.td-description').text(limit_text(line['description'], 50));
		tr.find('.td-description').attr('title', line['description']);
		
		this.determineButtons( tr );
		
		if (newRow) {
			$(this.table).find('.tbody-lines').append( tr );
		}
	};
	
	this.determineButtons = function(tr) {
		// batch marked as done? => no buttons
		if (this.opts.payment_import_status == 'done') {
			return;
		}
		
		
		var il = tr.data('pil');
		
		var tdButtons = tr.find('.td-action-buttons');
		tdButtons.empty();
		
		if (il['import_status'] == 'ready') {
			var btnImport = $('<input type="button" value="Import" />');
			btnImport.click(function(evt) {
				this.import_Click( evt.target );
			}.bind(this));
			tdButtons.append( btnImport );
		}
		if (il['import_status'] == 'ready' || il['import_status'] == 'unknown') {
			var btnSkip = $('<input type="button" value="Skip" />');
			btnSkip.click(function(evt) {
				this.skip_Click(evt.target);
			}.bind(this));
			tdButtons.append( btnSkip );
		}
		
		if (il['import_status'] == 'skip') {
			var btnUnskip= $('<input type="button" value="Unskip" />');
			btnUnskip.click(function(evt) {
				this.unskip_Click(evt.target);
			}.bind(this));
			tdButtons.append( btnUnskip );
		}
	};
	
	
	this.createHead = function(tbl) {
		var tr = $('<tr />');
		
		tr.append('<th>Status</th>');
		tr.append('<th>Customer</th>');
		tr.append('<th>Invoice</th>');
		tr.append('<th>Bankaccounts</th>');
		tr.append('<th class="amount">Amount</th>');
		tr.append('<th>Name</th>');
		tr.append('<th>Description</th>');
		tr.append('<th></th>');
		
		var thead = $('<thead />');
		thead.append(tr);

		tbl.append( thead );
	};
	
	
	this.render = function() {
		
		this.createTable();
		
	};
	
	
	this.import_Click = function(btn) {
		var tr = $(btn).closest('tr');
		var l = tr.data('pil');
		
		$(btn).prop('disabled', true);
		var me = this;
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=payment&c=import/stage&a=import'),
			data: {
				payment_import_line_id: l['payment_import_line_id']
			},
			success: function(data, xhr, textStatus) {
				if (data.success) {
					me.updateImportLines( data.payment_import_lines );
				} else {
					showAlert('Error', 'Error: ' + data.message);
				}
			}
		});
		
	};
	
	this.skip_Click = function(btn) {
		var tr = $(btn).closest('tr');
		var l = tr.data('pil');
		
		var me = this;
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=payment&c=import/stage&a=skip'),
			data: {
				payment_import_line_id: l['payment_import_line_id']
			},
			success: function(data, xhr, textStatus) {
				if (data.success) {
					me.updateImportLines( data.payment_import_lines );
				}
			}
		});
	};

	this.unskip_Click = function(btn) {
		var tr = $(btn).closest('tr');
		var l = tr.data('pil');
		
		var me = this;
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=payment&c=import/stage&a=unskip'),
			data: {
				payment_import_line_id: l['payment_import_line_id']
			},
			success: function(data, xhr, textStatus) {
				if (data.success) {
					me.updateImportLines( data.payment_import_lines );
				}
			}
		});
	};

	
	this.updateImportLines = function(pils) {
		for(var i in pils) {
			this.updateLine( pils[i] );
		}
	};
	
	
	this.customer_selection_Click = function(obj) {
		// batch done? => do nothing
		if (this.opts.payment_import_status == 'done') {
			return;
		}
		
		
		var tr = $(obj).closest('tr');
		var l = tr.data('pil');
		
		// select2 box already present?
		if (tr.find('.select-user').length)
			return;

		var plid = l['payment_import_line_id'];
		var customer_name = l['customer_name'];
		var person_id = l['person_id'];
		var company_id = l['company_id'];

		// remove old text
		tr.find('.customer-selection').empty();

		// add select2-box
		var s = $('<select class="select-user" name="customer_id_'+plid+'"></select>');
		var opt = $('<option value=""></option>');
		opt.text( customer_name );
		if (company_id) {
			opt.attr('value', 'company-'+company_id);
		} else if (person_id) {
			opt.attr('value', 'person-'+person_id);
		} else {
			opt.text('Maak uw keuze');
		}
		s.append(opt);
		tr.find('.customer-selection').append( s );

		// init select2
		$(s).select2({
			ajax: {
	    		url: appUrl('/?m=base&c=customer&a=select2'),
	    		type: 'POST',
	    		data: function(params) {
					var d = {};

	        		d.name = params.term;
	        		
	        		return d;
	    		}
			}
		});

		// handle customer selection
		var me = this;
		$(s).on("select2:select", function (e) {
			var v = $(this).val();
			var pil = $(this).closest('tr').data('pil');
			
			if (v != '') {
				me.set_customer( pil['payment_import_line_id'], v );
			}
		});
		
	};
	

	this.set_customer = function(plid, customer_id) {
		var me = this;
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=payment&c=import/stage&a=update_customer'),
			data: {
				payment_import_line_id: plid,
				customer_id: customer_id
			},
			success: function(data, xhr, textStatus) {
				if (data.success) {
					me.updateImportLines( data.payment_import_lines );
//					set_customer_info(plid, data);
				}
			}
		});
	}
	
	
	this.invoice_selection_Click = function(obj) {
		// batch done? => do nothing
		if (this.opts.payment_import_status == 'done') {
			return;
		}

		var tr = $(obj).closest('tr');
		var l = tr.data('pil');
		
		if (tr.find('.select-invoice').length > 0) {
			return;
		}

		var invoice_text = l['invoice_number'];
		var invoice_id = l['invoice_id'];
		var plid = l['payment_import_line_id'];

		// remove old text
		tr.find('.invoice-selection').empty();

		// add select2-box
		var s = $('<select class="select-invoice" name="invoice_id_'+plid+'"></select>');
		var opt = $('<option value=""></option>');
		opt.text( invoice_text );
		opt.attr('value', invoice_id);
		s.append(opt);
		tr.find('.invoice-selection').append( s );

		// init select2
		$(s).select2({
			ajax: {
	    		url: appUrl('/?m=invoice&c=invoice&a=select2'),
	    		type: 'POST',
	    		data: function(params) {
					var d = {};

					var l = $(this).closest('tr').data('pil')
					d.person_id = l['person_id'];
					d.company_id = l['person_id'];

	        		d.name = params.term;
	        		
	        		return d;
	    		}
			}
		});

		// handle customer selection
		var me = this;
		$(s).on("select2:select", function (e) {
			var v = $(this).val();
			var l = $(this).closest('tr').data('pil');
			
			if (v != '') {
				me.set_invoice( l['payment_import_line_id'], v );
			}
		});
	};
	

	this.set_invoice = function(plid, invoice_id) {
		var me = this;
		
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=payment&c=import/stage&a=update_invoice'),
			data: {
				payment_import_line_id: plid,
				invoice_id: invoice_id
			},
			success: function(data, xhr, textStatus) {
				if (data.success) {
					var cs = $('#line-'+plid).find('.invoice-selection');
					cs.text( data.invoice_number );
					cs.data('invoice-id', data.invoice_id);
					
					me.updateImportLines( data.payment_import_lines );
				}
			}
		});
	}

	
	
	
	this.init = function() {
		
	};
}





