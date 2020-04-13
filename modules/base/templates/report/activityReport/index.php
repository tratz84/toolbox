


<?= infopopup('Onderstaand overzicht benodigd technische kennis om volledig te begrijpen') ?>

<br/><br/>

<div id="activityreport-table-container"></div>




<script>

var t = new IndexTable('#activityreport-table-container');

t.setRowClick(function(row, evt) {
	show_popup(appUrl('/?m=base&c=report/activityReport&a=popup&id=' + $(row).data('record').activity_id));
});

t.setConnectorUrl( '/?m=base&c=report/activityReport&a=search' );


t.addColumn({
	fieldName: 'activity_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'username',
	fieldDescription: _('User'),
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'customer_name',
	fieldDescription: _('Name'),
	fieldType: 'text',
	searchable: true,
	render: function(record) {
		if (record.company_name) {
			return record.company_name;
		} else {
			var t = '';
			if (record.lastname) {
				t += record.lastname;
			}
			if (record.insert_lastname) {
				t += ', ' + record.insert_lastname;
			}
			if (record.firstname) {
				t += ' ' + record.firstname;
			}
			return t;
		}
	}
});
t.addColumn({
	fieldName: 'ref_object',
	fieldDescription: 'Object',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'ref_id',
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'short_description',
	fieldDescription: _('Short description'),
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'created',
	fieldDescription: _('Run on'),
	fieldType: 'datetime',
	searchable: false,
	skipSeconds: false
});


t.load();

</script>

