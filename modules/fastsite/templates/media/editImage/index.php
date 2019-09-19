

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=media/info&f='.urlencode($filename)) ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" onclick="saveImage();" class="fa fa-save"></a>
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

&nbsp;&nbsp;&nbsp;

Rotation

<input type="button" class="degrees-mutate" data-degrees="-90" value="&lt;&lt;" />
<input type="button" class="degrees-mutate" data-degrees="-1"  value="&lt;" />
<input type="range" name="degrees" value="0" min="0" max="360" />
<input type="button" class="degrees-mutate" data-degrees="1"   value="&gt;" />
<input type="button" class="degrees-mutate" data-degrees="90 " value="&gt;&gt;" />

&nbsp;&nbsp;&nbsp;

Width: <input type="text" style="width: 50px;" name="img_width" value="" />
&nbsp;
Height: <input type="text" style="width: 50px;" name="img_height" value="" />


<hr/>

<div id="editor-container"></div>


<script src="<?= BASE_HREF ?>module/fastsite/lib/media-edit-image.js"></script>
<script>

var mei;
$(document).ready(function() {
	mei = new MediaEditImage('#editor-container', <?= json_encode($imgUrl) ?>);

	$('[name=zoom]').change(function() {
		mei.setZoom( this.value );
	});
});

function saveImage() {
	
	formpost('/?m=fastsite&c=media/editImage&a=save', {
		zoom: mei.zoom,
		degrees: mei.degrees,
		cropx1: mei.getCropX1(),
		cropy1: mei.getCropY1(),
		cropx2: mei.getCropX2(),
		cropy2: mei.getCropY2(),
		img_width: $('[name=img_width]').val(),
		img_height: $('[name=img_height]').val(),
		f: <?= json_encode($filename) ?>
	});
}


</script>

