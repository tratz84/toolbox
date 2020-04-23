/**
 * 
 */


var selectedFilesyncWidget = null;
$(document).ready(function() {
	var anchorPagequeue = $('<a class="fa fa-picture-o"></a>');
	anchorPagequeue.attr('href', appUrl('/?m=filesync&c=pagequeue&a=upload'));
	
	$('.mobile-icon-container').append(anchorPagequeue);
	
	
	$('.filesync-select-widget .btnNewFile').click(function() {
		
	});
	
	$('.filesync-select-widget .btnExistingFile').click(function() {
		selectedFilesyncWidget = $(this).closest('.filesync-select-widget');
		
		show_popup( appUrl('/?m=filesync&c=archive&a=popup') );
		
		return false;
	});
});


function filesyncArchivePopup_Click( record ) {
	filesyncWidgetRender( selectedFilesyncWidget, record );
	
	close_popup();
}

function filesyncWidgetRender( widget, record ) {
	
	$(widget).find( 'input.input-value', record.store_file_id );
	
	
	
}




