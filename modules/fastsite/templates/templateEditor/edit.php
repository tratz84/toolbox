
<div class="page-header">
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


	<div style="position: relative;">
		<textarea id="content" style="width: 100%; height: 500px;"><?= esc_html($content) ?></textarea>
	</div>


	<script>
// 	$(document).ready(function() {
		var taContent = $('#content').get(0);
    	CodeMirror.fromTextArea(taContent, {
        	lineNumbers: true,
        	mode: 'text/html',
        	theme: 'ambiance'
    	});
// 	});
	</script>
<?php endif; ?>



