
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=store') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=filesync&c=pagequeue&a=upload') ?>" class="fa fa-plus"></a>
	</div>

	<h1>Page queue</h1>
</div>


<div id="page-table-container"></div>




<script>

var t = new IndexTable('#page-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=filesync&c=pagequeue&a=upload&id=' + $(row).data('record').pagequeue_id);
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
	fieldName: 'name',
	fieldDescription: 'Name',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'basename_file',
	fieldDescription: 'Bestandsnaam',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'edited',
	fieldDescription: 'Laatst bewerkt',
	fieldType: 'datetime',
	searchable: false
});

t.addColumn({
	fieldName: 'created',
	fieldDescription: 'Aangemaakt op',
	fieldType: 'datetime',
	searchable: false
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var pid = record['pagequeue_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=filesync&c=pagequeue&a=edit&id=' + pid));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=filesync&c=pagequeue&a=delete&id=' + pid));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.company_name);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>

