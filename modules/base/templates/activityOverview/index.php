

<div id="activity-table-container"></div>

<script>

var atc = new IndexTable('#activity-table-container', {
	autoloadNext: true
});

atc.setRowClick(function(row, evt) {
	show_popup(appUrl('/?m=base&c=report/activityReport&a=popup&id=' + $(row).data('record').activity_id));
});

atc.setConnectorUrl( '/?m=base&c=activityOverview&a=search&company_id=<?= (int)$companyId ?>&person_id=<?= (int)$personId ?>' );


atc.addColumn({
	fieldName: 'activity_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});
atc.addColumn({
	fieldName: 'code',
	fieldDescription: 'Code',
	fieldType: 'text',
	searchable: false
});
atc.addColumn({
	fieldName: 'username',
	fieldDescription: 'Gebruiker',
	fieldType: 'text',
	searchable: false
});
atc.addColumn({
	fieldName: 'short_description',
	fieldDescription: 'Omschrijving',
	fieldType: 'text',
	searchable: false
});
atc.addColumn({
	fieldName: 'created',
	fieldDescription: 'Tijdstip',
	fieldType: 'datetime',
	searchable: false
});

atc.load();

</script>