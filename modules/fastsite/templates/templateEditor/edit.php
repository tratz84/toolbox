
<div class="page-header">
	<h1>Template file editing</h1>
</div>


<div style="margin-bottom: 2em; font-style: italic;">
	File: <?= esc_html($templateName) ?>: <?= esc_html($file) ?>
</div>

<?php if (isset($error)) : ?>

	<div>
		An error has occured: <?= $error ?>
	</div>

<?php else : ?>

	<textarea style="width: 100%; height: 500px;"></textarea>

<?php endif; ?>



