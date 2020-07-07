

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=archive&a=upload&store_id='.$store->getStoreId()) ?>" class="fa fa-upload"></a>
		<a href="<?= appUrl('/?m=filesync&c=store') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('Files in') ?> <?= esc_html($store->getStoreName()) ?></h1>
</div>


<div id="storefile-table-container" class="autofocus-first-field"></div>


<script>

var t = new IndexTable('#storefile-table-container');

t.setRowClick(function(row, evt) {
	window.open( appUrl('/?m=filesync&c=storefile&a=download&inline=1&id=' + $(row).data('record').store_file_id), '_blank' );
});

t.setConnectorUrl( '/?m=filesync&c=storefile&a=search&storeId=<?= $store->getStoreId() ?>' );


// t.addColumn({
// 	fieldName: 'store_file_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });


t.addColumn({
	fieldName: 'document_date',
	fieldDescription: toolbox_t('Date'),
	fieldType: 'date'
});


t.addColumn({
	fieldName: 'company_name',
	fieldDescription: toolbox_t('Name'),
	fieldType: 'text',
	searchable: true,
	render: function(row) {
		return format_customername(row);
	}
});

t.addColumn({
	fieldName: 'path',
	fieldDescription: toolbox_t('Path'),
	fieldType: 'text',
	searchable: true
});

t.addColumn({
	fieldName: 'subject',
	fieldDescription: toolbox_t('Subject'),
	fieldType: 'text',
	searchable: true
});

t.addColumn({
	fieldName: 'filesize_text',
	fieldDescription: toolbox_t('File size'),
	fieldType: 'text'
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var store_file_id = record['store_file_id'];

		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=filesync&c=storefile&a=edit_meta&store_file_id=' + store_file_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=filesync&c=storefile&a=delete&store_file_id=' + store_file_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.fullname);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>

