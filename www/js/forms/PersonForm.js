


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



$(window).on('form-actions-set', function() {
	$('.base-form-select-company-list-edit .add-record').hide();
	
	$('.add-entry-container.action-box').append('<span><a class="select-company" href="javascript:void(0);">'+_('Select company')+'</a></span>');
	
	$('.base-form-select-company-list-edit .select-company').click(function() {
		show_popup( appUrl('/?m=base&c=company&a=popup') );
	});
	
});




