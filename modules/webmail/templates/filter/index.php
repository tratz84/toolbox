
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appurl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=webmail&c=filter&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1>Overzicht filters</h1>
</div>


<div id="filter-table-container"></div>




<script>

var t = new IndexTable('#filter-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=webmail&c=filter&a=edit&filter_id=' + $(row).data('record').filter_id);
});

t.setConnectorUrl( '/?m=webmail&c=filter&a=search' );
t.setSortUpdate(function(evt) {
	var ids = [];
	$('#filter-table-container tbody tr').each(function(index, node) {
		ids.push( $(node).data('record').filter_id );
	});

	$.ajax({
		url: appUrl('/?m=webmail&c=filter&a=sort'),
		type: 'POST',
		data: {
			ids: ids.join(',')
		}
	});
});

t.addColumn({
	fieldName: 'filter_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'name',
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
		var filter_id = record['filter_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=webmail&c=filter&a=edit&filter_id=' + filter_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=webmail&c=filter&a=delete&filter_id=' + filter_id));
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