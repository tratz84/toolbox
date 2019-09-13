

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=media/info&f='.urlencode($filename)) ?>" class="fa fa-chevron-circle-left"></a>
	</div>
	
	<h1>Image editor</h1>
</div>

<div class="form-generator">

	<div class="widget">
		<label>Filename</label>
		<span class="filename"><?= esc_html($filename) ?></span>
	</div>
	
</div>

<div class="clear"></div>

<hr/>
Zoom 
<select name="zoom">
	<option value="10">10%</option>
	<option value="20">20%</option>
	<option value="25">25%</option>
	<option value="50">50%</option>
	<option value="100" selected="selected">100%</option>
	<option value="200">200%</option>
</select>

Rotation 
<input type="range" value="0" min="0" max="360" />
<hr/>

<div id="editor-container"></div>


<script src="<?= BASE_HREF ?>module/fastsite/lib/media-edit-image.js"></script>
<script>

$(document).ready(function() {
	var mei = new MediaEditImage('#editor-container', <?= json_encode($imgUrl) ?>);

	$('[name=zoom]').change(function() {
		mei.setZoom( this.value );
	});
});


</script>

