

$(document).ready(function() {

	$('.add-entry-container .add-record').text('Prijsregel toevoegen');

	// 'Artikel toevoegen'-link
	// $('.add-entry-container').append(' | ');
	$('.add-entry-container').prepend('<span><a href="javascript:void(0);" id="addArticle">Artikel toevoegen</a></span>');
	$('#addArticle').click(function() {
		show_popup( appUrl('/?m=invoice&c=article&a=popup') );
	});


	// tekstregel toevoegen
	// $('.add-entry-container').append(' | ');
	$('.add-entry-container').append('<span><a href="javascript:void(0);" id="addTextLine">Tekstregel toevoegen</a></span>');
	$('#addTextLine').click(function() {
		var lefw = $('.invoice-form-list-offer-line-widget').get(0).lefw;
		lefw.addRecord(function(row) {
			$(row).find('.hidden-field-widget-line-type input').val('text');
		});
	});


	$('.invoice-form-list-offer-line-widget').get(0).lefw.setCallbackAddRecord(function(row) {
		fix_textLines();
	});

	$('.invoice-form-list-offer-line-widget').get(0).lefw.setCallbackDeleteRecord(function(row) {
		offer_calc_totals();
	});

	// customer-change, change popup upper right
	$('[name=customer_id]').change(function() {
		var id = $(this).val();

		loadCustomerDetails( id );
	});

	if ($('.rental-wizard-controller').length > 0) {
		// don't show customer details in offer wizard
	} else {
		loadCustomerDetails( $('[name=customer_id]').val() );
	}

	$('<th class="price-sum">Totaal</th>').insertBefore('.invoice-form-list-offer-line-widget thead tr th:last-child');
	
	fix_textLines();
});


function fix_textLines() {
// 	return;
	$('.invoice-form-list-offer-line-widget tbody tr').each(function(index, row) {
		var linetype = $(row).find('.hidden-field-widget-line-type input').val();
		if (linetype == 'text') {

        	$(row).find('.input-amount, .input-price, .input-vat').remove();
    //     	$(row).find('.input-short-description').attr('colspan', 4);
        	$(row).find('.input-short-description').find('input[type=text]').css('width', '100%');

        	$(row).find('.input-short-description2').attr('colspan', 3);
        	$(row).find('.input-short-description2').find('input[type=text]').css('width', '100%');
		} else {
			$(row).find('.input-short-description2').remove();
		}
		
		
		var articleId = $(row).find('.hidden-field-widget-article-id input[type=hidden]').val();
		if (articleId != '' && articleId != '0' && $(row).find('span.article-name').length == 0) {
			var inpShortDescription = $(row).find('.input-short-description input[type=text]');
			inpShortDescription.hide();
			
			var spanArticleName = $('<span class="article-name" />');
			spanArticleName.text( inpShortDescription.val() );
			$(row).find('.input-short-description').append( spanArticleName );
		}
		
		if ($(row).find('.price-sum').length == 0) {
			var tdAction = $(row).find('td.action');
			$('<td class="price-sum"></td>').insertBefore( tdAction );
		}
		
		
		// set events on change
		if (!$(row).data('price-calc-events-set')) {
			$(row).find('.input-amount input, .input-price input, .input-vat select').change(function() {
				offer_calc_totals();
			});
			
			$(row).data('price-calc-events-set', true);
		}
	});
	
	offer_calc_totals();
}


function offer_calc_totals() {

	var totalExclVat = 0;
	var totalInclVat = 0;
	var totalsByVat = {  };

	
	$('.invoice-form-list-offer-line-widget tbody tr').each(function(index, row) {
		if ($(row).find('.hidden-field-widget-line-type input').val() == 'text')
			return;
		
		var amount = strtodouble( $(row).find('.input-amount input[type=text]').val() );
		var price = strtodouble( $(row).find('.input-price input[type=text]').val() );
		var vatPercentage = strtodouble( $(row).find('.input-vat select').val() );
		vatPercentage = parseInt( vatPercentage * 100 );
		
		var p = Math.round(amount * price * 100);
		var vat = Math.round(p * vatPercentage / 10000);
		
		totalExclVat += p;
		totalInclVat += p + vat;
		
		if (!totalsByVat[vatPercentage]) totalsByVat[vatPercentage] = 0;
		totalsByVat[vatPercentage] += vat;
		
		$(row).find('td.price-sum').text( format_price((p+vat)/100, true, {'thousands': '.'}) );
	});
	
	var tfoot = $('.invoice-form-list-offer-line-widget tfoot');
	tfoot.empty();
	
	var trTotalExclVat = $('<tr><td colspan="6" align=right></td><td></td></tr>');
	trTotalExclVat.find('td:first-child').text('Totaal excl. btw ' + format_price(totalExclVat/100, true, {'thousands': '.'}));
	tfoot.append( trTotalExclVat );
	
	var keys = Object.keys( totalsByVat );
	keys.sort(function(v1, v2) {
		return v2 - v1;
	});
	
	for(var x=0; x < keys.length; x++) {
		var vat = keys[x];
		
		if (vat == 0) continue;
		
		var trVat = $('<tr><td colspan="6" align=right></td><td></td></tr>');
		trVat.find('td:first-child').text('Btw '+(vat/100)+'% ' + format_price(totalsByVat[vat]/100, true, {'thousands': '.'}));
		tfoot.append( trVat );
		
	}
	
	var trTotalInclVat = $('<tr><td colspan="6" align=right></td><td></td></tr>');
	trTotalInclVat.find('td:first-child').text('Totaal ' + format_price(totalInclVat/100, true, {'thousands': '.'}));
	tfoot.append( trTotalInclVat );
	
}



function print_Click() {
	var frm = $('.form-offer-form');
	var data = serialize2object( frm );

	formpost('/?m=invoice&c=offer&a=edit&print=1&id=' + $('[name=offer_id]').val(), data, { target: '_blank' });
}

function sendMail_Click() {
	var frm = $('.form-offer-form');
	var data = serialize2object( frm );

	formpost('/?m=invoice&c=offer&a=edit&sendmail=1&id=' + $('[name=offer_id]').val(), data, { target: '_blank' });
}


function article_Click(article) {

	// get 'ListEditFormWidget' instance (js/forms/form-actions.js)
	var lefw = $('.invoice-form-list-offer-line-widget').get(0).lefw;

	lefw.addRecord(function(row) {
		$(row).find('.hidden-field-widget-article-id input[type=hidden]').val( article.article_id );
		$(row).find('.hidden-field-widget-line-type input[type=hidden]').val( 'price' );
		$(row).find('.input-short-description input[type=text]').val( article.article_name );
		$(row).find('.input-price input[type=text]').val( format_price(article.price, true) );

		$(row).find('.input-vat select').val( article.vat_percentage );

		lefw.handleCounters();
	});


	close_popup();

}

function loadCustomerDetails( customerCode ) {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=invoice&c=offer&a=customer_data'),
		data: {
			customerCode: customerCode
		},
		success: function(data, xhr, textStatus) {
			$('#offer-customer').html( data );
		}
	});
}

function generateInvoice() {
	var invoiceId = $('#invoice_id').val();

	var t = '';
	if (!invoiceId) {
		t = 'Weet u zeker dat u een factuur wilt aanmaken voor deze offerte?';
	} else {
		t = 'Weet u zeker dat u <i>nogmaals</i> een factuur wilt aanmaken voor deze offerte?';
		t += '<br/><br/><a href="'+appUrl('/?m=invoice&c=invoice&a=edit&id='+invoiceId)+'">Bekijk factuur</a>';
	}
	
	showConfirmation('Factuur aanmaken', t, function() {
		var frm = $('.form-offer-form');
		var data = serialize2object( frm );

		formpost('/?m=invoice&c=offer&a=edit&generateInvoice=1&id=' + $('[name=offer_id]').val(), data, { target: '_blank' });
	});
}
