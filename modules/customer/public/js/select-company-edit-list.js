

$(window).on('form-actions-set', function() {
	// select-person-edit-list.js might be loaded when a 'Person'-object is loaded, because FormDbMapper initiates Form-objects
	// TODO: fix this that this js is only loaded at the right time
	if ($('.customer-form-select-company-list-edit').length == 0) {
		return;
	}
	
	// already set? this might called multiple times when popups are stacked
	if ($('.customer-form-select-company-list-edit .select-company').length > 0) {
		return;
	}

	var lefw = $('.customer-form-select-company-list-edit').get(0).lefw;
	
	$('.customer-form-select-company-list-edit .add-record').hide();
	
	$('.customer-form-select-company-list-edit .add-entry-container.action-box').append('<span><a class="select-company" href="javascript:void(0);">'+_('Select company')+'</a></span>');
	
	$('.customer-form-select-company-list-edit .select-company').click(function() {
		show_popup( appUrl('/?m=customer&c=company&a=popup') );
	});
	
	
	$(lefw).on('list-edit-add-record', function() {
		// remove duplicates
		var companyIds = [];
		$('.customer-form-select-company-list-edit tbody tr').each(function(index, node) {
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
	var names = $('.customer-form-select-company-list-edit').find('.input-company-name').find('.value');
	
	names.each(function(index, node) {
		if ($(node).hasClass('wrapped') == false) {
			$(node).wrap('<a class="link-company" href="javascript:void(0);"></a>');
			$(node).closest('.link-company').click(function() {
				var company_id = $(this).closest('tr').find('.hidden-field-widget-company-id input').val();
				
				window.open( appUrl('/?m=customer&c=company&a=edit&company_id=' + company_id), '_blank' );
			});
		}
	})
	
}


