


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
	var lefw = $('.base-form-select-company-list-edit').get(0).lefw;
	
	$('.base-form-select-company-list-edit .add-record').hide();
	
	$('.add-entry-container.action-box').append('<span><a class="select-company" href="javascript:void(0);">'+_('Select company')+'</a></span>');
	
	$('.base-form-select-company-list-edit .select-company').click(function() {
		show_popup( appUrl('/?m=base&c=company&a=popup') );
	});
	
	
	$(lefw).on('list-edit-add-record', function() {
		// remove duplicates
		var companyIds = [];
		$('.base-form-select-company-list-edit tbody tr').each(function(index, node) {
			var cid = $(node).find('.hidden-field-widget-company-id input').val();
			
			if (companyIds.indexOf(cid) != -1) {
				show_user_warning(_('Duplicate company'));
				$(node).remove();
			}
			
			companyIds.push( cid );
		});
		
		// wrap in anchors
		selectCompanyListEdit_WrapNames();
	});

	selectCompanyListEdit_WrapNames();
});


function selectCompanyListEdit_WrapNames() {
	var names = $('.base-form-select-company-list-edit').find('.input-company-name').find('.value');
	
	names.each(function(index, node) {
		if ($(node).hasClass('wrapped') == false) {
			$(node).wrap('<a class="link-company" href="javascript:void(0);"></a>');
			$(node).closest('.link-company').click(function() {
				var company_id = $(this).closest('tr').find('.hidden-field-widget-company-id input').val();
				
				window.open( appUrl('/?m=base&c=company&a=edit&company_id=' + company_id), '_blank' );
			});
		}
	})
	
}






