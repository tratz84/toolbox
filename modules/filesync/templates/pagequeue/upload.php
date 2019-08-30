
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=pagequeue') ?>" class="fa fa-chevron-circle-left"></a>
		<?php if ($isNew) : ?>
		<a href="javascript:void(0);" class="fa fa-upload submit-form"></a>
		<?php else : ?>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
		<?php endif; ?>
	</div>

	<h1>Pagina uploaden</h1>
</div>



<?= $form->render() ?>


<?php if (in_array($file_extension, array('jpg', 'jpeg'))) : ?>

<div id="editor-container"></div>

<script src="<?= appUrl('/module/filesync/js/exif.js') ?>"></script>
<script src="<?= appUrl('/module/filesync/js/image-editor.js') ?>"></script>
<script>

var ie = new DocumentImageEditor('#editor-container', { image_url: <?= json_encode(appUrl('/?m=filesync&c=pagequeue&a=download&id='.$pagequeue_id)) ?> });
ie.crop.pos1 = {
	x: parseInt($('[name=crop_x1]').val()) / 100 * ie.canvasSize,
	y: parseInt($('[name=crop_y1]').val()) / 100 * ie.canvasSize
};
if (isNaN(ie.crop.pos1.x)) ie.crop.pos1.x = 0;
if (isNaN(ie.crop.pos1.y)) ie.crop.pos1.y = 0;

ie.crop.pos2 = {
	x: parseInt($('[name=crop_x2]').val()) / 100 * ie.canvasSize,
	y: parseInt($('[name=crop_y2]').val()) / 100 * ie.canvasSize
};
if (isNaN(ie.crop.pos2.x)) ie.crop.pos2.x = 100;
if (isNaN(ie.crop.pos2.y)) ie.crop.pos2.y = 100;


ie.degrees = parseInt($('[name=degrees_rotated]').val());
if (isNaN(ie.degrees)) ie.degrees = 0;
ie.init();

$('.rotation-control [type=range]').val( ie.degrees );


$('.form-pagequeue-upload-form').submit(function() {
	$('[name=crop_x1]').val( ie.getCropX1() );
	$('[name=crop_y1]').val( ie.getCropY1() );
	$('[name=crop_x2]').val( ie.getCropX2() );
	$('[name=crop_y2]').val( ie.getCropY2() );
	$('[name=degrees_rotated]').val( ie.getDegreesRotated() );
});

</script>


<?php endif; ?>


