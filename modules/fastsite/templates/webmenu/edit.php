
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=webmenu') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<?php if ($isNew) : ?>
	<h1>Menu item aanmaken</h1>
	<?php else : ?>
	<h1>Menu item bewerken</h1>
	<?php endif; ?>
</div>



<?= $form->render() ?>
