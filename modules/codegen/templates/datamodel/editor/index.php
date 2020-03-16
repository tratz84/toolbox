
<script src="<?= appUrl('/?mpf=/module/codegen/js/datamodel-editor.js?v='.filemtime(module_file('codegen', 'public/js/datamodel-editor.js'))) ?>"></script>


<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=datamodel/module') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Datamodel - <?= esc_html($mod) ?></h1>
</div>

<form>

<div class="model-container">
	
</div>

</form>


<script>

var data_tablemodel = <?= json_encode($data_tablemodel) ?>;
var schemaname = <?= json_encode($mod) ?>;

var dmEditor;
$(document).ready(function() {
	dmEditor = new DatamodelEditor(schemaname, '.model-container');

	dmEditor.setData( data_tablemodel );
	
	dmEditor.render();
});


</script>




