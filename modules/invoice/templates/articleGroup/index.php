
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=invoice&c=articleGroup&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Overzicht groepen</h1>
</div>




<div id="article-group-table-container"></div>




<script>

var t = new IndexTable('#article-group-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=invoice&c=articleGroup&a=edit&article_group_id=' + $(row).data('record').article_group_id);
});

t.setConnectorUrl( '/?m=invoice&c=articleGroup&a=search' );


// t.addColumn({
// 	fieldName: 'article_group_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });
t.addColumn({
	fieldName: 'group_name',
	fieldDescription: 'Groepnaam',
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
		var article_group_id = record['article_group_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=invoice&c=articleGroup&a=edit&article_group_id=' + article_group_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=invoice&c=articleGroup&a=delete&article_group_id=' + article_group_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.group_name);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>