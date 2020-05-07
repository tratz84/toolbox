/**
 * 
 */


var selectedFilesyncWidget = null;

$(document).ready(function() {
	var anchorPagequeue = $('<a class="fa fa-picture-o"></a>');
	anchorPagequeue.attr('href', appUrl('/?m=filesync&c=pagequeue&a=upload'));
	
	$('.mobile-icon-container').append(anchorPagequeue);
	
	
	$('.filesync-select-widget .btnNewFile').click(function() {
		selectedFilesyncWidget = $(this).closest('.filesync-select-widget');
		
		var store_id = $(this).data('store-id');
		if (!store_id) store_id = '';
		
		show_popup( appUrl('/?m=filesync&c=archive&a=popup_new_file&store_id='+store_id) );
	});
	
	$('.filesync-select-widget .btnExistingFile').click(function() {
		selectedFilesyncWidget = $(this).closest('.filesync-select-widget');
		
		show_popup( appUrl('/?m=filesync&c=archive&a=popup') );
	});
	$('.filesync-select-widget .btnUnset').click(function() {
		selectedFilesyncWidget = $(this).closest('.filesync-select-widget');
		
		$(selectedFilesyncWidget).find('.preview-container').html('');
		$(selectedFilesyncWidget).find('input.input-value').val('');
	});

});


function filesyncArchiveFile_Select( storeFileId ) {
	filesyncWidgetRender( selectedFilesyncWidget, storeFileId );
	
	close_popup();
}

function filesyncWidgetRender( widget, storeFileId ) {
	
	$(widget).find( 'input.input-value' ).val( storeFileId );

	
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=filesync&c=archive&a=file_example'),
		data: {
			storeFileId: storeFileId
		},
		success: function(data, xhr, textStatus) {
			$(widget).find('.preview-container').html( data );
		}
	});
	
}

