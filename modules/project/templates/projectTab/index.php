

<div id="project-hour-tab-table-container"></div>

<script>

var t = new IndexTable('#project-hour-tab-table-container');


t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=project&c=projectHour&a=edit&project_hour_id=' + $(row).data('record').project_hour_id);
});


t.setConnectorUrl( '/?m=project&c=projectHour&a=search&company_id=<?= $companyId ?>&person_id=<?= $personId ?>' );


// t.addColumn({
// 	fieldName: 'offer_status_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });
t.addColumn({
	fieldName: 'username',
	fieldDescription: 'Gebruiker',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'project_id',
	fieldDescription: 'Project',
	fieldType: 'select',
	searchable: true,
	filterOptions: <?= json_encode($mapProjects) ?>,
	render: function(record) {
		return record.project_name;
	}
});
t.addColumn({
	fieldName: 'short_description',
	fieldDescription: 'Omschrijving',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'start_time',
	fieldDescription: 'Start',
	fieldType: 'datetime',
	searchable: false
});

t.addColumn({
	fieldName: 'end_time',
	fieldDescription: 'Eind',
	fieldType: 'datetime',
	searchable: false
});

t.addColumn({
	fieldName: 'duration',
	fieldDescription: 'Duur',
	fieldType: 'text',
	render: function(row) {
		return roundNumber(row.total_minutes/60, 2);
	},
	searchable: false
});


t.addColumn({
	fieldName: 'declarable',
	fieldDescription: 'Declarabel',
	fieldType: 'boolean',
	searchable: false
});

t.addColumn({
	fieldName: 'status_description',
	fieldDescription: 'Status',
	fieldType: 'text',
	searchable: false
});


t.addColumn({
	fieldName: '',
	fieldDescription: '',
	fieldType: 'actions',
	render: function( record ) {
		var project_hour_id = record['project_hour_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=project&c=projectHour&a=edit&project_hour_id=' + project_hour_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=project&c=projectHour&a=delete&project_hour_id=' + project_hour_id));
		anchDel.click( handle_deleteConfirmation_event );
		anchDel.data('description', record.description);

		
		var container = $('<div />');
		container.append(anchEdit);
		container.append(anchDel);
		
		return container;
	}
});

t.load();

</script>


