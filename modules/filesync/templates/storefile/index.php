

<div class="page-header">
	<div class="toolbox">
		<?php if ($store->getStoreType() == 'share') : ?>
		<a href="<?= appUrl('/?m=filesync&c=storefile&a=upload&store_id='.$store->getStoreId()) ?>" class="fa fa-upload"></a>
		<?php endif; ?>
		<a href="<?= appUrl('/?m=filesync&c=store') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('Files in') ?> <?= esc_html($store->getStoreName()) ?></h1>
</div>

<input type="hidden" id="storeType" value="<?= esc_attr($store->getStoreType()) ?>" />

<div id="storefile-table-container" class="autofocus-first-field"></div>


<script>

var t = new IndexTable('#storefile-table-container');

t.setRowClick(function(row, evt) {
	window.open( appUrl('/?m=filesync&c=storefile&a=download&inline=1&id=' + $(row).data('record').store_file_id), '_blank' );
});

t.setConnectorUrl( '/?m=filesync&c=storefile&a=search&storeId=<?= $store->getStoreId() ?>' );


t.addColumn({
	fieldName: 'store_file_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'path',
	fieldDescription: toolbox_t('Path'),
	fieldType: 'text',
	searchable: true
});

t.addColumn({
	fieldName: 'filesize_text',
	fieldDescription: toolbox_t('File size'),
	fieldType: 'text'
});

t.addColumn({
	fieldName: 'rev',
	fieldDescription: toolbox_t('Revision'),
	fieldType: 'text'
});

t.addColumn({
	fieldName: 'deleted',
	fieldDescription: toolbox_t('Deleted'),
	fieldType: 'boolean'
});

t.addColumn({
	fieldName: 'public',
	fieldDescription: toolbox_t('Public'),
	fieldType: 'boolean',
	searchable: true
});

t.addColumn({
	fieldName: 'lastmodified',
	fieldDescription: toolbox_t('Last changed'),
	fieldType: 'datetime'
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var store_file_id = record['store_file_id'];

		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=filesync&c=storefile&a=edit&store_file_id=' + store_file_id));
		
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

