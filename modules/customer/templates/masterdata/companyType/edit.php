
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=customer&c=masterdata/companyType') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1><?= t('Add Company type')?></h1>
    <?php else : ?>
    <h1><?= t('Edit Company type') ?></h1>
    <?php endif; ?>
</div>


<?php print $form->render() ?>

