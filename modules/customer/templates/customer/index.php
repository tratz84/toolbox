
<div class="page-header">
	<div class="toolbox list-toolbox">
		<?php if (hasCapability('customer', 'edit')) : ?>
		<a href="<?= appUrl('/?m=customer&c=customer&a=new') ?>" class="fa fa-plus" title="<?= t('New customer') ?>"></a>
		<?php endif; ?>
	</div>
	
    <h1><?= t('Overview customers') ?></h1>
</div>



<?= $cit->renderHtml() ?>


