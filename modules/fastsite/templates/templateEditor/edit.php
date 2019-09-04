
<div class="page-header">
	<?php if (isset($error) == false) : ?>
	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=templateEditor&n='.urlencode($templateName)) ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save"></a>
	</div>
	<?php endif; ?>
	
	<h1>Template file editing</h1>
</div>


<div style="margin-bottom: 2em; font-style: italic;">
	Template: <?= esc_html($templateName) ?>
	<br/>File: <?= esc_html($file) ?>
</div>

<?php if (isset($error)) : ?>

	<div>
		An error has occured: <?= $error ?>
	</div>

<?php else : ?>


    <link href="<?= BASE_HREF ?>module/fastsite/lib/codemirror/lib/codemirror.css" type="text/css" rel="stylesheet" />
    <script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/lib/codemirror.js" type="text/javascript"></script>
	<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/javascript/javascript.js"></script>
	<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/xml/xml.js"></script>
	<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/javascript/javascript.js"></script>
	<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/css/css.js"></script>
	<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/vbscript/vbscript.js"></script>
	<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/htmlmixed/htmlmixed.js"></script>
	<script src="<?= BASE_HREF ?>module/fastsite/lib/codemirror/mode/yaml/yaml.js"></script>
	

	<div style="height: 800px;">
		<textarea id="tacontent" name="tacontent" style="width: 100%; height: 800px;"><?= esc_html($content) ?></textarea>
	</div>


	<script>


	if (typeof less != 'undefined') {
		less.pageLoadFinished.then(function() {
			init_editor();
		});
	} else {
		$(document).ready(function() {
			init_editor();
		});
	}

	function init_editor() {
		var h = $(window).height();

		// add rule to last stylesheet for height editor
		var sn = document.styleSheets.length-1;
    	document.styleSheets[ sn ].addRule('.CodeMirror', 'height: ' + (h-280) + 'px');

    	
		var taContent = $('#tacontent');
    	CodeMirror.fromTextArea(taContent.get(0), {
        	lineNumbers: true,
        	mode: <?= json_encode($controller->editorMode($file)) ?>
    	});

	}
	</script>
<?php endif; ?>



