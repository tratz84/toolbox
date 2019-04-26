
<div class="page-header">

	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>

	<h1>Kies artikel</h1>

</div>


<div id="article-table-container"></div>




<script>

var t = new IndexTable('#article-table-container');

t.setRowClick(function(row, evt) {

	article_Click( $(row).data('record') );

});

t.setConnectorUrl( '/?m=invoice&c=article&a=search&active=1' );


t.addColumn({
	fieldName: 'article_id',
	width: 40,
	fieldDescription: 'Id',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'article_name',
	fieldDescription: 'Naam',
	fieldType: 'text',
	searchable: true
});
t.addColumn({
	fieldName: 'price',
	fieldDescription: 'Prijs excl. btw',
	fieldType: 'currency',
	searchable: false
});
t.addColumn({
	fieldName: 'vat_description',
	fieldDescription: 'Btw',
	fieldType: 'text',
	searchable: false
});
t.addColumn({
	fieldName: 'price_incl_vat',
	fieldDescription: 'Totaalprijs',
	fieldType: 'currency',
	searchable: false
});

t.load();

</script>
