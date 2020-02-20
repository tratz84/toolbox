/**
 * 
 */


function PaymentAutomatch() {
	
	this.callback_match = null;
	this.callback_done = null;
	this.pos = -1;
	this.autoNext = false;
	
	this.setCallbackMatch = function(func) {
		this.callback_match = func;
	};
	
	this.setCallbackDone = function(func) {
		this.callback_done = func;
	};
	
	
	this.matchPayment = function(paymentImportLineId) {
		$.ajax({
			type: 'POST',
			url: appUrl('/?m=payment&c=import/stage&a=match_line'),
			data: {
				payment_import_line_id: paymentImportLineId
			},
			success: function(data, xhr, textStatus) {
				if (this.callback_match) {
					this.callback_match( data );
				}
				
				this.matchNext();
			}.bind(this)
		});
	};
	
	
	this.matchNext = function() {
		// abort or not started?
		if (this.autoNext == false) {
			return;
		}
		
		this.pos++;
		
		var rows = $('.payment-import-table tbody.tbody-lines tr');
		
		// done?
		if (this.pos >= rows.length) {
			if (this.callback_done) {
				this.callback_done();
			}
			
			return;
		}
		
		
		var row = rows.get(this.pos);
		var payment_import_line = $(row).data('pil');
		
		this.matchPayment( payment_import_line.payment_import_line_id );
	};
	
	
	this.matchAll = function() {
		this.pos = -1;
		
		this.autoNext = true;
		
		this.matchNext();
		
//		this.alertDone();
	};
	
	
}

