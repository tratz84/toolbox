
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=webmail&c=template&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Templates</h1>
</div>



<div id="template-table-container"></div>




<script>

var t = new IndexTable('#template-table-container');


t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=webmail&c=template&a=edit&id=' + $(row).data('record').template_id);
});

t.setSortUpdate(function(evt) {
	var ids = [];
	$('#template-table-container tbody tr').each(function(index, node) {
		ids.push( $(node).data('record').template_id );
	});

	$.ajax({
		url: appUrl('/?m=webmail&c=template&a=sort'),
		type: 'POST',
		data: {
			ids: ids.join(',')
		}
	});
});

t.setConnectorUrl( '/?m=webmail&c=template&a=search' );


t.addColumn({
	fieldName: 'template_code',
	fieldDescription: 'Code',
	fieldType: 'text'
});
t.addColumn({
	fieldName: 'name',
	fieldDescription: 'Template naam',
	fieldType: 'text'
});
t.addColumn({
	fieldName: 'subject',
	fieldDescription: 'Onderwerp',
	fieldType: 'text'
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
		var template_id = record['template_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=webmail&c=template&a=edit&id=' + template_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=webmail&c=template&a=delete&id=' + template_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.template_code);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>