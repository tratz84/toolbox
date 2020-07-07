
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appurl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=webmail&c=connector&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
	<h1><?= t('Overview connectors') ?></h1>
</div>



<div id="connector-table-container"></div>




<script>

var t = new IndexTable('#connector-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=webmail&c=connector&a=edit&connector_id=' + $(row).data('record').connector_id);
});

t.setConnectorUrl( '/?m=webmail&c=connector&a=search' );


t.addColumn({
	fieldName: 'description',
	fieldDescription: toolbox_t('Description'),
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'hostname',
	fieldDescription: 'Hostname',
	fieldType: 'text',
	searchable: true
});

t.addColumn({
	fieldName: 'username',
	fieldDescription: toolbox_t('Username'),
	fieldType: 'text',
	searchable: true
});

t.addColumn({
	fieldName: 'active',
	fieldDescription: toolbox_t('Active'),
	fieldType: 'boolean',
	searchable: false
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var connector_id = record['connector_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=webmail&c=connector&a=edit&connector_id=' + connector_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=webmail&c=connector&a=delete&connector_id=' + connector_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.description);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>