/**
 * 
 */




$(window).on('form-actions-set', function() {
	$('.base-form-select-person-list-edit .add-record').hide();
	
	$('.add-entry-container.action-box').append('<span><a class="select-person" href="javascript:void(0);">'+_('Select person')+'</a></span>');
	
	$('.base-form-select-person-list-edit .select-person').click(function() {
		
		
		show_popup( appUrl('/?m=base&c=person&a=popup') );
		
	});
	
});

