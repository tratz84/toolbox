/**
 * 
 */


$(document).ready(function() {
	
	$('.widget.widget-end').prepend('<input class="btn-prev-month" type="button" value="<" title="Vorige maand" />');
	
	$('.widget.widget-end').append('<input class="btn-next-month" type="button" value=">" title="Volgende maand" />');

	$('.widget.widget-end').find('.btn-prev-month').click(function() {
		// determine current date
		var curdate = $('.widget-end input[name=end]').val();
		if (!curdate.match(/^\d{2}-\d{2}-\d{4}$/)) {
			curdate = $('input[name=start_upcoming_period]').val();
		}
		var date = text2date(curdate);
		
		// new date in box
		var newDate = new Date(date.getYear()+1900, date.getMonth(), 0);
		var newDateYmd = format_date( newDate, { ymdnumeric: true });
		
		// start period contract
		var startPeriod = text2date($('input[name=start_upcoming_period]').val());
		var startPeriodYmd = format_date(startPeriod, {ymdnumeric: true});
		
		
		if ( newDateYmd < startPeriodYmd ) {
			$('.widget-end input[name=end]').val( '' );
			
			generateOffer_updateLines();
		} else {
			$('.widget-end input[name=end]').val( format_date( newDate, {dmy: true} ) );
			
			generateOffer_updateLines();
		}
	});
	
	
	function btnNextMonth_Click() {
		var d = $('.widget-end input[name=end]').val();

		if (!d.match(/^\d{2}-\d{2}-\d{4}$/)) {
			d = $('input[name=start_upcoming_period]').val();
		}

		var date = text2date(d);
		var endContractDMY = $('[name=end_contract_dmy]').val();
		var endContract;
		if (endContractDMY && endContractDMY.match(/^\d\d-\d\d-\d\d\d\d$/)) {
			var tokensEndContract = endContractDMY.split('-');
			if (tokensEndContract.length == 3) {
				endContract = tokensEndContract[2] + '' + tokensEndContract[1] + '' + tokensEndContract[0];
			}
		}
		
		var newDate = new Date(date.getYear()+1900, date.getMonth()+1, 0);
		if (date.getMonth() == newDate.getMonth() && date.getDate() == newDate.getDate()) {
			newDate = new Date(date.getYear()+1900, date.getMonth()+2, 0);
		}

		var newDateYmd = format_date( newDate, { ymdnumeric: true });
		if ( endContract && newDateYmd >= endContract ) {
			$('.widget-end input[name=end]').val( endContractDMY );
			
			generateOffer_updateLines();
		} else {
			$('.widget-end input[name=end]').val( format_date( newDate, {dmy: true} ) );
			
			generateOffer_updateLines();
		}
	};
	$('.widget.widget-end').find('.btn-next-month').click( btnNextMonth_Click );
	
	
	$('[name=end]').on('dp.change', function() {
		generateOffer_updateLines();
	});
	
	
	$('.widget.list-edit-form-widget').on('handleCountersExecuted', function() {
		$('.invoiceform-list-offer-line-widget tr').each(function(index, node) {
			var linetype = $(node).find('.hidden-field-widget-line-type input[type=hidden]').val();
			if (linetype == 'rental')
				$(node).find('.row-delete').hide();
		});
	});

	$('.widget.list-edit-form-widget').get(0).lefw.handleCounters();
	
	if ($('[name=first_visit]').val() == '1') {
		$('[name=first_visit]').val( '0' );
		btnNextMonth_Click();
	}
});


var xhr_generateOffer_updateLines=null;
function generateOffer_updateLines() {
	if (xhr_generateOffer_updateLines) {
		xhr_generateOffer_updateLines.abort();
	}
	
	var data = {
			m: 'rental',
			end: $('[name=end]').val()
	}
	
	// contract wizard
	data['formid'] = $('#formid').val();
	data['c'] = 'contract/wizard';
	data['a'] = 'offer_generateOfferLines';
	
	
	xhr_generateOffer_updateLines = $.ajax({
		url: appUrl('/'),
		type: 'POST',
		data: data,
		success: function(data, xhr, textStatus) {
			var col = new ContractOfferLines();
			if (data.error) {
				col.setOfferLines( [] );
				show_user_error( data.message );
			} else {
				col.setOfferLines( data.offerLines );
			}
		}
	});
}


function ContractOfferLines() {
	
	this.pos = 0;
	this.offerLines = [];
	this.lefw = $('.invoiceform-list-offer-line-widget').get(0).lefw;
	
	this.setOfferLines = function(offerLines) {
		
		$('.invoiceform-list-offer-line-widget tr').each(function(index, node) {
//			console.log(node);
			if ($(node).find('.hidden-field-widget-line-type input[type=hidden]').val() == 'rental') {
				$(node).remove();
			}
		});
		
		this.pos = 0;
		this.offerLines = offerLines;
		
		if (offerLines.length > 0) {
			this.lefw.addRecord(function(row) {
				var rentalLines = [];
				
				rentalLines.push(row);
				
				$(row).prependTo( $(row).closest('tbody') );
				
				for(var x=1; x < this.offerLines.length; x++) {
					var nextRow = $(row).clone();
					
					$(row).closest('tbody').prepend(nextRow);
					
					rentalLines.push(nextRow);
				}
				
				for(var x=this.offerLines.length-1, rowNo=0; x >= 0; x--, rowNo++) {
					var rr = rentalLines[ rowNo ];
					
					var il = this.offerLines[ x ];
					
					$(rr).find('.hidden-field-widget-article-id input[type=hidden]').val( il.article_id );
					$(rr).find('.hidden-field-widget-line-type input[type=hidden]').val( 'rental' );
					$(rr).find('.hidden-field-widget-meta input[type=hidden]').val( il.start + ' ' + il.end );
					$(rr).find('.input-short-description input[type=text]').val( il.description );
					$(rr).find('.input-amount input[type=text]').val( il.amount );
					$(rr).find('.input-price input[type=text]').val( format_price(il.price, true) );
				}
				
				fix_textLines();
				
				this.lefw.handleCounters();
			}.bind(this));
		}
	};
}






