
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=customer&c=person&a=edit') ?>" class="fa fa-user" title="<?= t('New person') ?>"></a>
		<a href="<?= appUrl('/?m=customer&c=company&a=edit') ?>" class="fa fa-building-o" title="<?= t('New company') ?>"></a>
	</div>
	
    <h1><?= t('Overview customers') ?></h1>
</div>



<div id="person-table-container"></div>




<script>

var t = new IndexTable('#person-table-container');

t.setRowClick(function(row, evt) {
	var type = $(row).data('record').type;
	
	if (type == 'company') {
    	window.location = appUrl('/?m=customer&c=company&a=edit&company_id=' + $(row).data('record').id);
	}
	else if (type == 'person') {
    	window.location = appUrl('/?m=customer&c=person&a=edit&person_id=' + $(row).data('record').id);
	}
});

t.setConnectorUrl( '/?m=customer&c=customer&a=search' );


t.addColumn({
	fieldName: 'type',
	width: 100,
	fieldDescription: 'Type',
	fieldType: 'text',
	searchable: false,
	render: function(record) {
		if (record.type == 'person') {
			return _('Private customer');
		}
		else if (record.type == 'company') {
			return _('Business customer');
		}
		else {
			return record.type;
		}
	}
});

t.addColumn({
	fieldName: 'name',
	fieldDescription: _('Customer name'),
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'contact_person',
	fieldDescription: _('Contact person'),
	fieldType: 'text',
	searchable: true
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var id = record['id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');

		if (record.type == 'company')
			anchEdit.attr('href', appUrl('/?m=customer&c=company&a=edit&company_id=' + id));
		if (record.type == 'person')
			anchEdit.attr('href', appUrl('/?m=customer&c=person&a=edit&person_id=' + id));

		var anchDel  = $('<a class="fa fa-trash" />');
		if (record.type == 'company')
			anchDel.attr('href', appUrl('/?m=customer&c=company&a=delete&company_id=' + id));
		if (record.type == 'person')
			anchDel.attr('href', appUrl('/?m=customer&c=person&a=delete&person_id=' + id));
		
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