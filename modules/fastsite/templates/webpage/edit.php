
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=webpage') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<?php if ($isNew) : ?>
		<h1>Webpagina toevoegen</h1>
	<?php else : ?>
		<h1>Webpagina bewerken</h1>
	<?php endif; ?>
</div>


<?= $form->render() ?>


