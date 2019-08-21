
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=webpage&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1>Webpages</h1>

</div>




<div id="webpage-table-container"></div>




<script>

var t = new IndexTable('#webpage-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=fastsite&c=webpage&a=edit&id=' + $(row).data('record').webpage_id);
});

t.setConnectorUrl( '/?m=fastsite&c=webpage&a=search' );


t.addColumn({
	fieldName: 'webpage_id',
	width: 40,
	fieldDescription: 'id',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'code',
	fieldDescription: 'Code',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'url',
	fieldDescription: 'Url',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'meta_title',
	fieldDescription: 'Title',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var webpage_id = record['webpage_id'];
		var page_no = record['page_no'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=fastsite&c=webpage&a=edit&id=' + webpage_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=fastsite&c=webpage&a=delete&id=' + webpage_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.url + ' - ' + record.meta_title);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>
