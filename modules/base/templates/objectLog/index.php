
<div class="page-header">
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>

	<h1>Log</h1>
</div>



<div id="object-log-table-container"></div>

<script>

var altc = new IndexTable('#object-log-table-container', {
	autoloadNext: true
});

altc.setConnectorUrl( <?= json_encode('/?m=base&c=objectLog&a=search&object_name='.$object_name.'&object_id='.$object_id) ?> );


altc.addColumn({
	fieldName: 'created',
	width: 160,
	fieldDescription: _('Created'),
	fieldType: 'timestamp',
	searchable: false,
});

altc.addColumn({
	fieldName: 'object_action',
	fieldDescription: _('Action'),
	fieldType: 'text',
	searchable: false,
	render: function(row) {
		return _('object_action.'+row.object_action);
	}
});
altc.addColumn({
	fieldName: 'object_key',
	width: 200,
	fieldDescription: _('Label'),
	fieldType: 'text',
	searchable: false,
	render: function(row) {
		return _('fieldname.'+row.object_key);
	}
});
altc.addColumn({
	fieldName: 'value_old',
	fieldDescription: _('Old value'),
	fieldType: 'text',
	searchable: false
});
altc.addColumn({
	fieldName: 'value_new',
	fieldDescription: _('New value'),
	fieldType: 'text',
	searchable: false
});

altc.load();

</script>