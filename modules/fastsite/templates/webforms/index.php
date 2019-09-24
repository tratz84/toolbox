
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=webforms&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1><?= t('Overview forms') ?></h1>
</div>

<div id="webforms-table-container"></div>




<script>

var t = new IndexTable('#webforms-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=fastsite&c=webforms&a=edit&webform_id=' + $(row).data('record').webform_id);
});

t.setConnectorUrl( '/?m=fastsite&c=webforms&a=search' );


t.addColumn({
	fieldName: 'webform_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'webform_name',
	fieldDescription: 'Naam',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'active',
	fieldDescription: 'Actief',
	fieldType: 'boolean',
	searchable: false
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var webform_id = record['webform_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=base&c=webforms&a=edit&webform_id=' + webform_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=base&c=webforms&a=delete&webform_id=' + webform_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.webform_name);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>