

<div class="page-header">

	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>

	<h1><?= t('Select file') ?></h1>
</div>



<div id="filesync-popup-table"></div>


<script>

var fpt = new IndexTable('#filesync-popup-table');

fpt.setRowClick(function(row, evt) {
	
	<?= $js_callback_func ?>( $(row).data('record').store_file_id );
	
});

fpt.setConnectorUrl( '/?m=filesync&c=storefile&a=search&archiveOnly=1' );


// t.addColumn({
// 	fieldName: 'store_file_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });

fpt.addColumn({
	fieldName: 'store_name',
	fieldDescription: 'Store',
	fieldType: 'text'
});


fpt.addColumn({
	fieldName: 'document_date',
	fieldDescription: toolbox_t('Date'),
	fieldType: 'date'
});



fpt.addColumn({
	fieldName: 'company_name',
	fieldDescription: toolbox_t('Name'),
	fieldType: 'text',
	searchable: true,
	render: function(row) {
		return format_customername(row);
	}
});

fpt.addColumn({
	fieldName: 'path',
	fieldDescription: toolbox_t('Path'),
	fieldType: 'text',
	searchable: true
});

fpt.addColumn({
	fieldName: 'subject',
	fieldDescription: toolbox_t('Subject'),
	fieldType: 'text',
	searchable: true
});

fpt.addColumn({
	fieldName: 'filesize_text',
	fieldDescription: toolbox_t('File size'),
	fieldType: 'text'
});

fpt.addColumn({
	fieldName: 'actions',
	fieldDescription: '',
	fieldType: 'actions',
	render: function(rec) {
		var aView = $('<a href="javascript:void(0);" class="fa fa-search" />');
		aView.click(function() {
			var r = $(this).closest('tr').data('record');
			
			var url = appUrl('/?m=filesync&c=storefile&a=download&inline=1&id=' + r.store_file_id);
			window.open( url, '_blank' );
		});
		
		
		var c = $('<div />');
		c.append( aView );
		return c;
	}
});

fpt.load();

</script>

