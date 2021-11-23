
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=dataimport&c=formSelect') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<h1><?= t('Import data') ?>: <?= esc_html($dif['description']) ?></h1>
</div>


<?= $form->render() ?>

