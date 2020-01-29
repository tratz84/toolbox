

<div class="page-header">
	<div class="toolbox">
	
		<?php if (hasCapability('invoice', 'import-payments')) : ?>
		<a href="<?= appUrl('/?m=invoice&c=payment/import') ?>" class="fa fa-download" title="Import"></a>
		<?php endif; ?>
	</div>

	<h1><?= t('Overview payments') ?></h1>
</div>






