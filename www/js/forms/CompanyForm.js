


$(document).ready(function() {
	
	
	cf_validateIban();
	$('.form-generator.form-company-form [name=iban]').change(function() {
		cf_validateIban();
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

