

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=calendar&c=calendar&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Kalenders</h1>
</div>





<div id="calendar-table-container"></div>




<script>

var t = new IndexTable('#calendar-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=calendar&c=calendar&a=edit&id=' + $(row).data('record').calendar_id);
});

t.setConnectorUrl( '/?m=calendar&c=calendar&a=search' );


t.addColumn({
	fieldName: 'calendar_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'name',
	fieldDescription: 'Kalendernaam',
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
		var calendar_id = record['calendar_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=calendar&c=calendar&a=edit&id=' + calendar_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=calendar&c=calendar&a=delete&id=' + calendar_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.name);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>