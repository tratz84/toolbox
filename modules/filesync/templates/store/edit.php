

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=store') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1><?= t('New store') ?></h1>
    <?php else : ?>
    <h1><?= t('Edit store') ?></h1>
    <?php endif; ?>
</div>

<?php print $form->render() ?>

