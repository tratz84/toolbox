
<div class="page-header">

	<div class="toolbox">
		<?php if (hasCapability('payment', 'edit-payments')) : ?>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
		<?php endif; ?>
	</div>

	<?php if ($isNew) : ?>
	<h1>Nieuwe betaling</h1>
	<?php else : ?>
	<h1>Betaling bewerken</h1>
	<?php endif; ?>
</div>



<?= $form->render() ?>

