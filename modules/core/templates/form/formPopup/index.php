
<div class="page-header">
	<div class="toolbox">
		<a alt="Sluit, zonder opslaan" title="Sluit, zonder opslaan" href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
		<a alt="Opslaan" title="Opslaan" href="javascript:void(0);" class="fa fa-save popup-save-link"></a>
	</div>

	<?php if ($isNew) : ?>
	   <h1><?= t('Add') ?></h1>
	<?php else : ?>
		<h1><?= t('Edit2') ?></h1>
	<?php endif; ?>
</div>

<div class="popup-error-list-container"></div>

<?= $form->render() ?>

