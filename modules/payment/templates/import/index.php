


<div class="page-header">
	
	<div class="toolbox">
	
		<a href="<?= appUrl('/?m=payment&c=paymentOverview') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Betalingen importeren</h1>
</div>


<?= $form->render() ?>




<br/>
<br/>
<hr/>
<br/>

<h1><?= t('Previous imports') ?></h1>
<br/>

<div id="pi-table-container"></div>




<script>

var t = new IndexTable('#pi-table-container');

t.setRowClick(function(row, evt) {
	window.location = appUrl('/?m=payment&c=import/stage&id=' + $(row).data('record').payment_import_id);
});

t.setConnectorUrl( '/?m=payment&c=import&a=search' );


t.addColumn({
	fieldName: 'payment_import_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'description',
	fieldDescription: 'Omschrijving',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'status',
	fieldDescription: 'Status',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'count',
	fieldDescription: 'Count',
	fieldType: 'text',
	searchable: false
});


t.load();

</script>

