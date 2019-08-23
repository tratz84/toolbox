
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=docqueue&c=list') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-upload submit-form"></a>
	</div>

	<h1>Document uploaden</h1>
</div>



<?= $form->render() ?>


<?php if (in_array($file_extension, array('jpg', 'jpeg'))) : ?>

<div id="editor-container"></div>

<script src="<?= appUrl('/module/docqueue/js/image-editor.js') ?>"></script>
<script>

var ie = new DocumentImageEditor('#editor-container', { image_url: <?= json_encode(appUrl('/?m=docqueue&c=list&a=download&id='.$document_id)) ?> });



</script>


<?php endif; ?>


