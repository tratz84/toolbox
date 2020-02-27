

<div class="page-header">
    <div class="toolbox">
        <a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
        <a href="javascript:void(0);" onclick="save_filearchive_settings();" class="fa fa-save"></a>
    </div>
    
    <h1>Filesync: archive store settings</h1>
</div>



<?= $form->render() ?>



<script>

function save_filearchive_settings() {
    $.ajax({
        url: appUrl('/?m=filesync&c=dashboard/archiveWidget&a=settings'),
        type: 'POST',
        data: serialize2object('.form-filesync-archive-settings-form')
    });
    
    var selected_store_id = $('.form-filesync-archive-settings-form [name=store_id]').val();
    
    $('.form-archive-file-upload-form select[name=store_id]').val( selected_store_id );
    
    close_popup();
}

</script>


