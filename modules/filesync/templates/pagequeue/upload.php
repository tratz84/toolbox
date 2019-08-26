
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=pagequeue&c=list') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-upload submit-form"></a>
	</div>

	<h1>Pagina uploaden</h1>
</div>



<?= $form->render() ?>


<?php if (in_array($file_extension, array('jpg', 'jpeg'))) : ?>

<div id="editor-container"></div>

<script src="<?= appUrl('/module/filesync/js/exif.js') ?>"></script>
<script src="<?= appUrl('/module/filesync/js/image-editor.js') ?>"></script>
<script>

$(window).ready(function() {
    var ie = new DocumentImageEditor('#editor-container', { image_url: <?= json_encode(appUrl('/?m=filesync&c=pagequeue&a=download&id='.$pagequeue_id)) ?> });
});



</script>


<?php endif; ?>


