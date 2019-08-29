
<div class="toolbox">
	<a href="<?= appUrl('/?m=filesync&c=pagequeue&a=delete&id='.$pagequeue_id) ?>" class="fa fa-trash"></a>
</div>
<h2>Bewerken</h2>

<?= $form->render() ?>

<br/>

<div id="editor-container"></div>

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



$('.form-pagequeue-edit-form').find('[type=text], textarea').on('change', function() {
	saveEditorData();
});

</script>

