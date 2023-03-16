

<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=group') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
		
	</div>

	<?php if ($group->isNew()) : ?>
		<h1><?= t('Add group') ?></h1>
	<?php else : ?>
		<h1><?= t('Edit group') ?></h1>
	<?php endif; ?>
</div>



<?= $form->render() ?>


