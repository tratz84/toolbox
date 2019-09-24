
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=store') ?>" class="fa fa-chevron-circle-left"></a>
<?php /*		<a href="<?= appUrl('/?m=filesync&c=pagequeue') ?>" class="fa fa-picture-o"></a> */ ?>
		<a href="<?= appUrl('/?m=filesync&c=pagequeue&a=upload') ?>" class="fa fa-plus"></a>
	</div>

	<h1>Page queue - PDF creator</h1>
</div>



<div class="pagequeue-index-container">
	<div id="page-table-container"></div>
	
	<div id="page-selection-container">
		<ul class="selected-files sortable-container"></ul>
		
		<div class="action-container" style="display: none;">
			<a href="javascript:void(0);" onclick="generate_pdf();">Genereer PDF</a>
			|
			<a href="javascript:void(0);" onclick="generate_pdf({delete_files: true});">Genereer PDF &amp; verwijder bestanden</a>
		</div>
		
		<br/>
		<div id="page-sample"></div>
	</div>
</div>


<script src="<?= appUrl('/module/filesync/js/image-editor.js') ?>"></script>

<script>

var t = new IndexTable('#page-table-container');

t.setRowClick(function(row, evt) {
	
	var record = $(row).data('record');

	// file already selected? => skip
	if ($('.selected-files').find('.file-' + record.pagequeue_id).length) {
    	return;
	}
	
	var n = record.name ? record.name : record.filename;
	
	var li = $('<li />');
	li.data('record', record);
	li.css('cursor', 'pointer');
	li.click(function() {
		showPageSample( $(this).data('record') );
	});
	li.addClass('file-' + record.pagequeue_id);
	li.append($('<a href="javascript:void(0);" class="fa fa-remove" />'));
	li.append($('<div class="fa fa-sort handler-sortable ui-sortable-handle" />'));
	li.append($('<span/>').text(n));

	$(li).find('.fa-remove').click(function() {
		$(this).closest('li').remove();
		
		toggle_action_container();
		$('#page-sample').html('');
	});

	$('.selected-files').append( li );

	toggle_action_container();

	showPageSample( record );
});

t.setConnectorUrl( '/?m=filesync&c=pagequeue&a=search' );

t.addColumn({
	fieldName: 'pagequeue_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'info',
	fieldDescription: 'Omschrijving',
	fieldType: 'text',
	searchable: false,
	render: function(row) {
		var c = $('<div />');

		if (row.name && row.name != '') {
			c.append($('<span class="pagequeue-name-'+row.pagequeue_id+'" />').text(row.name));
		} else {
			c.append($('<span class="pagequeue-name-'+row.pagequeue_id+'" />').text(row.filename));
		}

		var t = $('<div />');
		t.append( format_datetime(str2datetime(row.created)) );
		c.append(t);
		
		return c;
	}
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var pagequeue_id = record['pagequeue_id'];
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', 'javascript:void(0);');
		anchDel.data('id', pagequeue_id);
		anchDel.click( function() {
			var id = $(this).data('id');

			$.ajax({
				url: appUrl('/?m=filesync&c=pagequeue&a=delete'),
				data: {
					id: id
				},
				success: function() {
        			$('#page-selection-container .selected-files .file-' + id).remove();
        
        			if (currentPagequeue && currentPagequeue.pagequeue_id == id) {
        				$('#page-sample').html('');
        				currentPagequeue = null;
        			}
        
        			$(this).closest('tr').remove();

        			toggle_action_container();
				}.bind(this),
				error: function() {
					showAlert('Error', 'Error deleting page');
				}
			});
			
			
			return false;
		} );

		
		var container = $('<div />');
		container.append(anchDel);
		
		return container;
	}
});


t.load();


function toggle_action_container() {
	if ($('.selected-files li').length) {
		$('.action-container').show();
	} else {
		$('.action-container').hide();
	}
}


function generate_pdf(opts) {
	opts = opts ? opts : {};
	
	var pqIds = new Array();
	
	$('.selected-files li').each(function(index, node) {
		var record = $(node).data('record');

		pqIds.push( record.pagequeue_id );
	});

	if (pqIds.length == 0) {
		alert('Geen PDF\'s geselecteerd');
		return;
	}

	formpost('/?m=filesync&c=pagequeue&a=pdf_generate', {
		ids: pqIds.join(','),
		delete_files: opts.delete_files?1:0
	});
}


var ie, currentPagequeue;
function showPageSample(pagequeue) {
	currentPagequeue = pagequeue;
	
	$('#page-sample').html('');
	var name = pagequeue.name ? pagequeue.name : pagequeue.filename;
	$('#page-sample').append($('<div style="font-weight: bold; margin-bottom: 3px;" />').text(name));
	ie = new DocumentImageEditor('#page-sample', { image_url: appUrl('/?m=filesync&c=pagequeue&a=download&id='+pagequeue.pagequeue_id) });
// 	ie.readonly = true;
	
	ie.crop.pos1 = { x: pagequeue.crop_x1 / 100 * ie.canvasSize, y: pagequeue.crop_y1 / 100 * ie.canvasSize };
	ie.crop.pos2 = { x: pagequeue.crop_x2 / 100 * ie.canvasSize, y: pagequeue.crop_y2 / 100 * ie.canvasSize };
	ie.degrees = pagequeue.degrees_rotated;
	
	ie.init();
	
	$('.rotation-control [type=range]').val( ie.degrees );
}

$(document).on('image-editor-changed', function() {
	currentPagequeue.crop_x1 = ie.getCropX1();
	currentPagequeue.crop_y1 = ie.getCropY1();
	currentPagequeue.crop_x2 = ie.getCropX2();
	currentPagequeue.crop_y2 = ie.getCropY2();
	currentPagequeue.degrees_rotated = ie.getDegreesRotated();
	
	ie.saveEditorData( currentPagequeue );
});



</script>

