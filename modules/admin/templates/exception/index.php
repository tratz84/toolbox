
<style type="text/css">

.exception-container label { font-weight: bold; margin-bottom: 0; }
.exception-container .item { margin-bottom: 10px; }

</style>


<div class="page-header">
    <h1>Overzicht Exceptions</h1>
</div>



<div id="exception-table-container"></div>




<script>

var t = new IndexTable('#exception-table-container');

t.setRowClick(function(row, evt) {
// 	window.location = appUrl('/?m=admin&c=company&a=edit&company_id=' + $(row).data('record').company_id);
	show_popup( appUrl('/?m=admin&c=exception&a=popup&id=' + $(row).data('record').exception_log_id) );
});

t.setConnectorUrl( '/?m=admin&c=exception&a=search' );


t.addColumn({
	fieldName: 'exception_log_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'contextName',
	fieldDescription: 'Context',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'user_id',
	fieldDescription: 'User id',
	fieldType: 'text',
	searchable: false
});

t.addColumn({
	fieldName: 'request_uri',
	fieldDescription: 'Url',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'message',
	fieldDescription: 'Bericht',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'created',
	fieldDescription: 'Aangemaakt op',
	fieldType: 'datetime',
	searchable: false
});

t.load();

</script>