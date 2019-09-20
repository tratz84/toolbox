
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=webforms') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<?php if ($isNew) : ?>
	<h1><?= t('Add form') ?></h1>
	<?php else : ?>
	<h1><?= t('Edit form') ?></h1>
	<?php endif; ?>
</div>


<?= $form->render() ?>
