

<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=pagequeue') ?>" class="fa fa-picture-o"></a>
		<a href="<?= appUrl('/?m=filesync&c=store&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1>Overzicht stores</h1>
</div>


<div id="store-table-container"></div>


<script>

var t = new IndexTable('#store-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=filesync&c=storefile&id=' + $(row).data('record').store_id);
});

t.setConnectorUrl( '/?m=filesync&c=store&a=search' );


// t.addColumn({
// 	fieldName: 'store_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });

t.addColumn({
	fieldName: 'store_type',
	fieldDescription: 'Type',
	fieldType: 'text'
});

t.addColumn({
	fieldName: 'store_name',
	fieldDescription: 'Naam',
	fieldType: 'text'
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	width: 100,
	render: function( record ) {
		var store_id = record['store_id'];

		var anchSearch = $('<a class="fa fa-search" />');
		anchSearch.attr('href', appUrl('/?m=filesync&c=storefile&id=' + store_id));

		var anchMaintenance = $('<a class="fa fa-cog" />');
		anchMaintenance.attr('href', appUrl('/?m=filesync&c=store&a=maintenance&store_id=' + store_id));

		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=filesync&c=store&a=edit&store_id=' + store_id));

		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=filesync&c=store&a=delete&store_id=' + store_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.fullname);


		var container = $('<div />');
		container.append(anchSearch);
		container.append(anchMaintenance);
		container.append(anchEdit);
		container.append(anchDel);

		return container;
	}
});

t.load();

</script>
