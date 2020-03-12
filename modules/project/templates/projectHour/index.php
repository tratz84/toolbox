
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=project&c=project') ?>" class="fa fa-chevron-circle-left"></a>
		<?php if (isset($project)) : ?>
		<a href="<?= appUrl('/?m=project&c=project&a=edit&project_id='.$project->getProjectId()) ?>" class="fa fa-cog" title="Project info"></a>
		<?php endif; ?>
		<a href="<?= appUrl('/?m=project&c=projectHour'.($project_id?'&project_id='.$project_id:'').($company_id?'&company_id='.$company_id:'').($person_id?'&person_id='.$person_id:'').'&a=edit') ?>" class="fa fa-plus"></a>
	</div>

	<h1>
		Overzicht
		<?php if (isset($project)) : ?> 
		<?= esc_html($project->getProjectName()) ?>
		<?php endif; ?>
	</h1>
</div>


<div id="project-hour-table-container"></div>

<script>

var t = new IndexTable('#project-hour-table-container');


t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=project&c=projectHour&a=edit&project_hour_id=' + $(row).data('record').project_hour_id);
});


t.setConnectorUrl( '/?m=project&c=projectHour&a=search&project_id=<?= $project_id ?>&company_id=<?= $company_id ?>&person_id=<?= $person_id ?>&date=<?= $date ?>' );


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

<?php if (!$company_id && !$person_id && !$project_id) : ?>
t.addColumn({
	fieldName: 'name',
	fieldDescription: 'Naam',
	fieldType: 'text',
	searchable: false,
	render: function(row) {
		return format_customername(row);
	}
});
<?php endif; ?>

t.addColumn({
	fieldName: 'project_name',
	fieldDescription: 'Project',
	fieldType: 'text',
	searchable: false
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


