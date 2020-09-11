/**
 * 
 */




$(window).on('form-actions-set', function() {
	// select-person-edit-list.js might be loaded when a 'Company'-object is loaded, because FormDbMapper initiates Form-objects
	// TODO: fix this that this js is only loaded at the right time
	if ($('.customer-form-select-person-list-edit').length == 0) {
		return;
	}
	
	// already set? this might called multiple times when popups are stacked
	if ($('.customer-form-select-person-list-edit .select-person').length > 0) {
		return;
	}
	
	var lefw = $('.customer-form-select-person-list-edit').get(0).lefw;
	
	$('.customer-form-select-person-list-edit .add-record').hide();
	
	$('.customer-form-select-person-list-edit .add-entry-container.action-box').append('<span><a class="select-person" href="javascript:void(0);">'+_('Select person')+'</a></span>');
//	$('.customer-form-select-person-list-edit .add-entry-container.action-box').append('<span><a class="add-person" href="javascript:void(0);">'+_('Add person')+'</a></span>');
	
	$('.customer-form-select-person-list-edit .select-person').click(function() {
		show_popup( appUrl('/?m=customer&c=person&a=popup'), {
			renderCallback: function(popup) {

				$(popup).find('.toolbox .submit-form').hide();
				
				$(popup).find('a[data-toggle="tab"]').on('shown.bs.tab', function() {
					var personSelected = $(this).closest('.popup-container').find('a[data-tab-name=select-person]').attr('aria-selected');
					
					if (personSelected == 'true') {
						$(this).closest('.popup-container').find('.toolbox .submit-form').hide();
					}
					else {
						$(this).closest('.popup-container').find('.toolbox .submit-form').show();
					}
				});

				$(popup).find('.submit-form').click(function() {
					$.ajax({
						type: 'POST',
						url: appUrl('/?m=customer&c=popup/newCustomer&a=save_person'),
						data: $('.popup-container .form-person-form').serialize(),
						success: function(data, xhr, textStatus) {
							if (data.error) {
								console.log(data);
								setPopupFormErrors($(popup).find('.tab-name-popup-add-person'), data.errors);
							}
							
							if (data.success) {
								var lefw = $('.customer-form-select-person-list-edit').get(0).lefw;
						
								lefw.addRecord(function(row) {
									$(row).find('.hidden-field-widget-person-id input').val( data.person_id );
									$(row).find('.widget-full-name input').val( data.person_name );
									$(row).find('.widget-full-name .value').text( data.person_name );
								});

								
								close_popup();
							}
							
						}
					});
					
					return false;
				});
			}
		} );
	});

	$('.customer-form-select-person-list-edit .add-person').click(function() {
		show_popup( appUrl('/?m=customer&c=popup/newCustomer&personOnly=1'), {
			renderCallback: function(popup) {
				$(popup).find('.submit-form').click(function() {
					$.ajax({
						type: 'POST',
						url: appUrl('/?m=customer&c=popup/newCustomer&a=save_person'),
						data: $('.popup-container .form-person-form').serialize(),
						success: function(data, xhr, textStatus) {
							if (data.error) {
								console.log(data);
								setPopupFormErrors($(popup).find('.tab-name-person'), data.errors);
							}
							
							if (data.success) {
								var lefw = $('.customer-form-select-person-list-edit').get(0).lefw;
						
								lefw.addRecord(function(row) {
									$(row).find('.hidden-field-widget-person-id input').val( data.person_id );
									$(row).find('.widget-full-name input').val( data.person_name );
									$(row).find('.widget-full-name .value').text( data.person_name );
								});

								
								close_popup();
							}
							
						}
					});
				});
			}
		});
	});
	
	$(lefw).on('list-edit-add-record', function() {
		// remove duplicates
		var personIds = [];
		$('.customer-form-select-person-list-edit tbody tr').each(function(index, node) {
			var pid = $(node).find('.hidden-field-widget-person-id input').val();
			
			if (personIds.indexOf(pid) != -1) {
				show_user_warning(_('Duplicate person'));
				$(node).remove();
			}
			
			personIds.push( pid );
		});
		
		// wrap in anchors
		selectPersonListEdit_WrapNames();
	});
	
	selectPersonListEdit_WrapNames();
});

function selectPersonListEdit_WrapNames() {
	var names = $('.customer-form-select-person-list-edit').find('.input-full-name').find('.value');
	
	names.each(function(index, node) {
		if ($(node).hasClass('wrapped') == false) {
			$(node).wrap('<a class="link-person" href="javascript:void(0);"></a>');
			$(node).closest('.link-person').click(function() {
				var person_id = $(this).closest('tr').find('.hidden-field-widget-person-id input').val();
				
				window.open( appUrl('/?m=customer&c=person&a=edit&person_id=' + person_id), '_blank' );
			});
		}
	})
	
}

