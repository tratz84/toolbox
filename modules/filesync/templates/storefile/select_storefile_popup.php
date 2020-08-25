
<div class="page-header">
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>

	<h1><?= t('Select file') ?></h1>
</div>



<nav>
	<div class="nav nav-tabs" id="nav-tab" role="tablist">
		<a class="nav-item nav-link active" id="existing-file-tab"  data-toggle="tab" role="tab" aria-controls="existing-customer" href="#nav-existing-file" aria-selected="true"><?= t('Existing file') ?></a>
		<a class="nav-item nav-link" id="new-file-tab"       data-toggle="tab" role="tab" aria-controls="new-company"       href="#nav-new-file"       aria-selected="false"><?= t('New file') ?></a>
	</div>
</nav>



<div class="tab-content" id="nav-tabContent">
	<div class="tab-pane fade show active" id="nav-existing-file" role="tabpanel" aria-labelledby="existing-file-tab">
		<div id="popup-storefile-table-container" class="autofocus-first-field"></div>
	</div>
	
	<div class="tab-pane fade" id="nav-new-file" role="tabpanel" aria-labelledby="new-file-tab">
		<?= $form->render() ?>
	</div>	
</div>




<script>

var sfpt = new IndexTable('#popup-storefile-table-container');

sfpt.setRowClick(function(row, evt) {
	select_store_file_callback( $(row).data('record') );
	close_popup();
});

sfpt.setConnectorUrl( '/?m=filesync&c=storefile&a=search&store_type=share' );


sfpt.addColumn({
	fieldName: 'store_name',
	fieldDescription: 'Store',
	fieldType: 'text',
	searchable: false
});

sfpt.addColumn({
	fieldName: 'path',
	fieldDescription: toolbox_t('Path'),
	fieldType: 'text',
	searchable: true
});

sfpt.addColumn({
	fieldName: 'filesize_text',
	fieldDescription: toolbox_t('File size'),
	fieldType: 'text'
});

sfpt.addColumn({
	fieldName: 'rev',
	fieldDescription: toolbox_t('Revision'),
	fieldType: 'text'
});

sfpt.addColumn({
	fieldName: 'deleted',
	fieldDescription: toolbox_t('Deleted'),
	fieldType: 'boolean'
});

sfpt.addColumn({
	fieldName: 'public',
	fieldDescription: toolbox_t('Public'),
	fieldType: 'boolean',
	searchable: true
});

sfpt.addColumn({
	fieldName: 'lastmodified',
	fieldDescription: toolbox_t('Last changed'),
	fieldType: 'datetime'
});

sfpt.load();


// handle file submit
$('.form-store-file-upload-form').submit(function() {
	var fd = new FormData( $('.popup-container form.form-store-file-upload-form').get(0) );
	
    $.ajax({
        url: appUrl('/?m=filesync&c=storefile&a=upload&r=json'),
        data: fd,
        processData: false,
		contentType: false,
		cache: false,
        type: 'POST',
        success: function ( data ) {
            if (data.success) {
                // some callback
                select_store_file_callback( data );

                close_popup();
            }
            if (data.error) {
                var str = '';
                for(var i in data.errors) {
                    str = str + data.errors[i] + "\n";
                }

                alert(str);
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            alert('Error: ' + xhr.responseText);
        }
    });
	
	return false;
});



</script>

