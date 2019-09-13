
<div class="page-header">

	<div class="toolbox">
		<a href="javascript:void(0);" onclick="confirm_delete();" class="fa fa-trash"></a>
	
		<a href="<?= appUrl('/?m=fastsite&c=media/files') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>File info</h1>
</div>


<div class="form-generator">

	<div class="widget">
		<label>Filename</label>
		<span class="filename"><?= esc_html($filename) ?></span>
	</div>
	
	<div class="widget">
		<label>Path</label>
		<?= esc_html($path) ?>
	</div>
	
	<div class="widget">
		<label>Filesize</label>
		<?= format_filesize( $filesize ) ?>
	</div>
	
	<div class="widget">
		<label>Url</label>
		<a href="<?= esc_attr(BASE_HREF . 'fs-media/' . $filename) ?>" target="_blank">
			<?= esc_attr(BASE_HREF . 'fs-media/' . $filename) ?>
		</a>
	</div>

</div>

<?php if (in_array(file_extension($filename), ['jpg', 'png', 'jpeg', 'gif'])) : ?>
<div class="clear">
	<hr/>
	<img src="/fs-media/<?= esc_attr($path) ?>" style="max-width: 50%; height: auto;" />
</div>
<?php endif; ?>



<script>

function confirm_delete() {
	var filename = $('.widget span.filename').text();
	
	showConfirmation('Delete file', 'Are you sure to delete "'+filename+'" ?', function() {
		window.location = appUrl('/?m=fastsite&c=media/files&a=delete&f=' + encodeURI(filename));
	});
	
}

</script>




