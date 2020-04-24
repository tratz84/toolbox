

<div class="page-header">

	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>

	<h1><?= t('Select file') ?></h1>
</div>



<div id="filesync-popup-table"></div>


<script>

var fpt = new IndexTable('#filesync-popup-table');

fpt.setRowClick(function(row, evt) {
	
	filesyncArchiveFile_Select( $(row).data('record').store_file_id );
	
});

fpt.setConnectorUrl( '/?m=filesync&c=storefile&a=search&archiveOnly=1' );


// t.addColumn({
// 	fieldName: 'store_file_id',
// 	width: 40,
// 	fieldDescription: 'Id',
// 	fieldType: 'text',
// 	searchable: false
// });

fpt.addColumn({
	fieldName: 'store_name',
	fieldDescription: 'Store',
	fieldType: 'text'
});


fpt.addColumn({
	fieldName: 'document_date',
	fieldDescription: 'Datum',
	fieldType: 'date'
});



fpt.addColumn({
	fieldName: 'company_name',
	fieldDescription: 'Naam',
	fieldType: 'text',
	searchable: true,
	render: function(row) {
		return format_customername(row);
	}
});

fpt.addColumn({
	fieldName: 'path',
	fieldDescription: 'pad',
	fieldType: 'text',
	searchable: true
});

fpt.addColumn({
	fieldName: 'subject',
	fieldDescription: 'Onderwerp',
	fieldType: 'text',
	searchable: true
});

fpt.addColumn({
	fieldName: 'filesize_text',
	fieldDescription: 'Bestandsgrootte',
	fieldType: 'text'
});

fpt.load();

</script>

