


$(document).ready(function() {
	
	
	cf_validateIban();
	$('.form-generator.form-company-form [name=iban]').change(function() {
		cf_validateIban();
	});
	
	
	$('.form-generator.form-company-form [name=vat_number]').change(function() {
		cf_validateVatNumber();
	});
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
		url: appUrl('/?m=base&c=company'),
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

