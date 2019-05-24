
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=base&c=masterdata/companyType&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1><?= t('Company Types')?></h1>
</div>



<div id="company-type-table-container"></div>




<script>

var t = new IndexTable('#company-type-table-container');


t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=base&c=masterdata/companyType&a=edit&id=' + $(row).data('record').company_type_id);
});

t.setSortUpdate(function(evt) {
	var ids = [];
	$('#company-type-table-container tbody tr').each(function(index, node) {
		ids.push( $(node).data('record').company_type_id );
	});

	$.ajax({
		url: appUrl('/?m=base&c=masterdata/companyType&a=sort'),
		type: 'POST',
		data: {
			ids: ids.join(',')
		}
	});
});

t.setConnectorUrl( '/?m=base&c=masterdata/companyType&a=search' );


// t.addColumn({
// 	fieldName: 'company_type_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });
t.addColumn({
	fieldName: 'type_name',
	fieldDescription: '<?= t('Description') ?>',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'default_selected',
	fieldDescription: '<?= t('Default selected') ?>',
	fieldType: 'boolean',
	searchable: false
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var id = record['company_type_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=base&c=masterdata/companyType&a=edit&id=' + id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=base&c=masterdata/companyType&a=delete&id=' + id));
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