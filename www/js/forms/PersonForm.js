


$(document).ready(function() {
	
	
	cp_validateIban();
	$('.form-generator.form-person-form [name=iban]').change(function() {
		cp_validateIban();
	});
	
});



function cp_validateIban() {
	var inp = $('.form-generator.form-person-form [name=iban]');
	
	var val = inp.val();
	val = val.replace(/\s+/, '');
	
	var r = IBAN.isValid( val );
	
	if (val == '' || r) {
		inp.css('border-color', '');
	} else {
		inp.css('border-color', '#f00');
	}
	
}

