/**
 * 
 */




$(window).on('form-actions-set', function() {
	// select-person-edit-list.js might be loaded when a 'Company'-object is loaded, because FormDbMapper initiates Form-objects
	// TODO: fix this that this js is only loaded at the right time
	if ($('.customer-form-select-person-list-edit').length == 0) {
		return;
	}
	
	var lefw = $('.customer-form-select-person-list-edit').get(0).lefw;
	
	$('.customer-form-select-person-list-edit .add-record').hide();
	
	$('.customer-form-select-person-list-edit .add-entry-container.action-box').append('<span><a class="select-person" href="javascript:void(0);">'+_('Select person')+'</a></span>');
	
	$('.customer-form-select-person-list-edit .select-person').click(function() {
		show_popup( appUrl('/?m=customer&c=person&a=popup') );
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

