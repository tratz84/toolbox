

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=invoice&c=article&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Artikelen</h1>
</div>



<div id="article-table-container"></div>




<script>

var t = new IndexTable('#article-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=invoice&c=article&a=edit&id=' + $(row).data('record').article_id);
});

t.setConnectorUrl( '/?m=invoice&c=article&a=search' );


// t.addColumn({
// 	fieldName: 'article_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });
t.addColumn({
	fieldName: 'article_name',
	fieldDescription: 'Naam',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'price',
	fieldDescription: 'Prijs',
	fieldType: 'currency',
	searchable: false
});
t.addColumn({
	fieldName: 'vat_description',
	fieldDescription: 'Btw',
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
		var article_id = record['article_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=invoice&c=article&a=edit&id=' + article_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=invoice&c=article&a=delete&id=' + article_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.article_name);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>