

<div class="page-header">
	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<h1><?= t('Upload file') ?></h1>
</div>



<?= $form->render() ?>


<script>

$('.popup-container .submit-form').click(function() {
	var fd = new FormData( $('.popup-container form.form-archive-file-upload-form').get(0) );
	
    $.ajax({
        url: appUrl('/?m=filesync&c=archive&a=upload&r=json'),
        data: fd,
        processData: false,
		contentType: false,
		cache: false,
        type: 'POST',
        success: function ( data ) {
            if (data.success) {
                // some callback
                filesyncArchiveFile_Select( data.storeFileId );

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
	
	
});

</script>

