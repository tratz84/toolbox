
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=admin&c=user&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1>Overzicht gebruikers</h1>
</div>



<div id="user-table-container"></div>




<script>

var t = new IndexTable('#user-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=admin&c=user&a=edit&user_id=' + $(row).data('record').user_id);
});

t.setConnectorUrl( '/?m=admin&c=user&a=search' );

// t.addColumn({
// 	fieldName: 'user_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });
t.addColumn({
	fieldName: 'username',
	fieldDescription: 'Gebruikersnaam',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'user_type',
	fieldDescription: 'Type',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var user_id = record['user_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=admin&c=user&a=edit&user_id=' + user_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=admin&c=user&a=delete&user_id=' + user_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.username);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>