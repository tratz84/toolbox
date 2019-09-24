
<script src="<?= appUrl('/module/filesync/js/image-editor.js') ?>"></script>


<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=store') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=filesync&c=pagequeue&a=pdf') ?>" class="fa fa-code-fork"></a>
		<a href="<?= appUrl('/?m=filesync&c=pagequeue&a=upload') ?>" class="fa fa-plus"></a>
	</div>

	<h1>Page queue - image viewer</h1>
</div>


<div class="pagequeue-index-container">
	<div id="page-table-container"></div>
	
	<div id="page-editor-container">
	</div>
</div>





<script>

var t = new IndexTable('#page-table-container');

t.setRowClick(function(row, evt) {
//	window.location = appUrl('/?m=filesync&c=pagequeue&a=upload&id=' + $(row).data('record').pagequeue_id);

	$.ajax({
		url: appUrl('/?m=filesync&c=pagequeue&a=edit'),
		data: {
			id: $(row).data('record').pagequeue_id
		},
		success: function(data, xhr, textStatus) {
			$('#page-editor-container').html( data );
		}
	});
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



$(document).on('image-editor-changed', function() {
	var formdata = $('.form-pagequeue-edit-form').serialize();
	ie.saveEditorData( formdata );
});


</script>

