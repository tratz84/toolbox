<div class="page-header">

	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=customer&c=person&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1><?= t('Overview persons') ?></h1>
</div>



<div id="person-table-container"></div>




<script>

var t = new IndexTable('#person-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=customer&c=person&a=edit&person_id=' + $(row).data('record').person_id);
});

t.setConnectorUrl( '/?m=customer&c=person&a=search' );


// t.addColumn({
// 	fieldName: 'person_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });

t.addColumn({
	fieldName: 'lastname',
	fieldDescription: '<?= t('Name') ?>',
	fieldType: 'text',
	render: function(record) {console.log(record);
		var t = '';
		if (record.lastname)
			t += record.lastname;
		if (record.insert_lastname && record.insert_lastname.match(/\S+/)) {
			t += ', ' + record.insert_lastname;
		}
		
		return t;
	},
	searchable: true
});

t.addColumn({
	fieldName: 'firstname',
	fieldDescription: '<?= t('Firstname') ?>',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var person_id = record['person_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=customer&c=person&a=edit&person_id=' + person_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=customer&c=person&a=delete&person_id=' + person_id));
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