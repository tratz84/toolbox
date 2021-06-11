
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<?php if ($isNew) : ?>
	<h1>New ..</h1>
	<?php else : ?>
	<h1>Edit ..</h1>
	<?php endif; ?>
</div>



<?= $form->render() ?>


