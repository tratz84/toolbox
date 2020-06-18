


$(document).ready(function() {
	
	
	cf_validateIban();
	$('.form-generator.form-company-form [name=iban]').change(function() {
		cf_validateIban();
	});
	
	
	var objVatnr = $('.form-generator.form-company-form [name=vat_number]');
	objVatnr.change(function() {
		cf_validateVatNumber();
	});
	
	var anchLookup = $('<a href="javascript:void(0);" class="fa fa-search"></a>');
	anchLookup.click(function() {
		var nr = $('[name=vat_number]').val();
		
		if ($.trim(nr) == '') {
			showAlert('Geen nummmer ingevoerd', 'Geen BTW nummer ingevoerd');
			return;
		}
		
		window.open( appUrl('/?m=customer&c=company&a=view_vat_number&nr=' + encodeURI(nr)), '_blank' );
	});
	objVatnr.parent().append('&nbsp;');
	objVatnr.parent().append( anchLookup );
	
});



function cf_validateIban() {
	var inp = $('.form-generator.form-company-form [name=iban]');
	
	var val = inp.val();
	console.log(val);
	val = val.replace(/\s+/, '');
	
	var r = IBAN.isValid( val );
	
	if (val == '' || r) {
		inp.css('border-color', '');
	} else {
		inp.css('border-color', '#f00');
	}
	
}

function cf_validateVatNumber() {
	var vatnr = $('[name=vat_number]').val();

	var v = $.trim(vatnr);
	if (v == '') {
		$('[name=vat_number]').css('border', '');
		return;
	}
	
	$.ajax({
		url: appUrl('/?m=customer&c=company'),
		data: {
			a: 'check_vat_number',
			vat_number: vatnr
		},
		success: function(xhr, data, textStatus) {
			if (xhr.success) {
				$('[name=vat_number]').css('border', '');
			} else {
				$('[name=vat_number]').css('border', '1px solid #f00');
				showInlineWarning('Let op, btw-nummer verificatie mislukt', { timeout: 2000 });
			}
		}
	});
}

