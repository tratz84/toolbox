
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=webmail&c=externalTokens') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1><?= t('Add external token') ?></h1>
    <?php else : ?>
    <h1><?= t('Edit external token') ?></h1>
    <?php endif; ?>
</div>


<?php print $form->render() ?>

