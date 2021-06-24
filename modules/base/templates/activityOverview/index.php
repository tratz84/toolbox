

<div id="activity-table-container" class="<?= $stretchtobottom ? 'stretch-to-bottom' : '' ?>"></div>

<script>

var atc = new IndexTable('#activity-table-container', {
	autoloadNext: true
});

atc.setRowClick(function(row, evt) {
	show_popup(appUrl('/?m=base&c=report/activityReport&a=popup&id=' + $(row).data('record').activity_id));
});

atc.setConnectorUrl( '/?m=base&c=activityOverview&a=search&company_id=<?= (int)$companyId ?>&person_id=<?= (int)$personId ?>&ref_object=<?= urlencode($ref_object) ?>&ref_id=<?= $ref_id ?>' );


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
	fieldDescription: '<?= t('User') ?>',
	fieldType: 'text',
	searchable: false
});
atc.addColumn({
	fieldName: 'short_description',
	fieldDescription: '<?= t('Description') ?>',
	fieldType: 'text',
	searchable: false
});
atc.addColumn({
	fieldName: 'created',
	fieldDescription: '<?= t('Time') ?>',
	fieldType: 'datetimesec',
	searchable: false
});

atc.load();

</script>