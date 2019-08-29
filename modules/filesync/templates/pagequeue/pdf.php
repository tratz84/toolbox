
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=store') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=filesync&c=pagequeue') ?>" class="fa fa-picture-o"></a>
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
		</div>
	</div>
</div>



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
	li.addClass('file-' + record.pagequeue_id);
	li.append($('<a href="javascript:void(0);" class="fa fa-remove" />'));
	li.append($('<div class="fa fa-sort handler-sortable ui-sortable-handle" />'));
	li.append($('<span/>').text(n));

	$(li).find('.fa-remove').click(function() {
		$(this).closest('li').remove();
		
		toggle_action_container();
	});

	$('.selected-files').append( li );

	toggle_action_container();
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
		
		return c;
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


function generate_pdf() {
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
		ids: pqIds.join(',')
	});
}



</script>

