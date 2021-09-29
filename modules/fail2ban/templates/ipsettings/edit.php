
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=fail2ban&c=ipsettings') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<?php if ($isNew) : ?>
	<h1><?= t('New IP settings') ?></h1>
	<?php else : ?>
	<h1><?= t('Edit IP settings') ?></h1>
	<?php endif; ?>
</div>



<?= $form->render() ?>


