
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=docqueue&c=list&a=upload') ?>" class="fa fa-plus"></a>
	</div>

	<h1>Document queue</h1>
</div>


<div id="document-table-container"></div>




<script>

var t = new IndexTable('#document-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=docqueue&c=list&a=upload&id=' + $(row).data('record').document_id);
});

t.setConnectorUrl( '/?m=docqueue&c=list&a=search' );

t.addColumn({
	fieldName: 'document_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'name',
	fieldDescription: 'Name',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'basename_file',
	fieldDescription: 'Bestandsnaam',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'edited',
	fieldDescription: 'Laatst bewerkt',
	fieldType: 'datetime',
	searchable: false
});

t.addColumn({
	fieldName: 'created',
	fieldDescription: 'Aangemaakt op',
	fieldType: 'datetime',
	searchable: false
});

t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var document_id = record['document_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=docqueue&c=document&a=edit&id=' + document_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=docqueue&c=document&a=delete&id=' + document_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.company_name);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>

