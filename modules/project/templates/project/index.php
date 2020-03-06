

<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=project&c=project&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1>Projecten</h1>
</div>






<div id="project-table-container"></div>




<script>

var t = new IndexTable('#project-table-container');


t.setRowClick(function(row, evt) {

	if ($(evt.target).hasClass('td-name') || $(evt.target).closest('.td-name').length) {
		return;
	}

	window.location = appUrl('/?m=project&c=projectHour&project_id=' + $(row).data('record').project_id);
});


t.setConnectorUrl( '/?m=project&c=project&a=search' );


// t.addColumn({
// 	fieldName: 'offer_status_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });
t.addColumn({
	fieldName: 'name',
	fieldDescription: 'Klant',
	fieldType: 'text',
	render: function(row) {
		if (row.company_name) {
			return '<a href="'+appUrl('/?m=project&c=projectHour&company_id='+row.company_id)+'">'+row.company_name+'</a>';
		} else {
			return '<a href="'+appUrl('/?m=project&c=projectHour&person_id='+row.person_id)+'">'+format_customername(row)+'</a>';
		}
	},
	searchable: true
});
t.addColumn({
	fieldName: 'project_name',
	fieldDescription: 'Project',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'total_minuts',
	fieldDescription: 'Duur',
	fieldType: 'text',
	render: function(row) {
		return format_number(row.total_minutes/60, {thousands: '.'});
	},
	searchable: false
});

t.addColumn({
	fieldName: 'project_hours',
	fieldDescription: 'Max. hours',
	fieldType: 'text',
	render: function(record) {
		if (record.project_billable_type == 'fixed') {
			return record.project_hours;
		}
	},
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
		var project_id = record['project_id'];
		
		var anchEdit = $('<a class="fa fa-pencil" />');
		anchEdit.attr('href', appUrl('/?m=project&c=project&a=edit&id=' + project_id));
		
		var anchDel  = $('<a class="fa fa-trash" />');
		anchDel.attr('href', appUrl('/?m=project&c=project&a=delete&id=' + project_id));
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


