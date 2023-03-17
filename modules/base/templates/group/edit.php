

<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=group') ?>" class="fa fa-chevron-circle-left"></a>
		<?php if ($group->isNew() == false) : ?>
		<a href="<?= appUrl('/?m=base&c=group&a=delete&id='.$group->getUserGroupId()) ?>" class="fa fa-trash delete"></a>
		<?php endif; ?>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
		
	</div>

	<?php if ($group->isNew()) : ?>
		<h1><?= t('Add group') ?></h1>
	<?php else : ?>
		<h1><?= t('Edit group') ?></h1>
	<?php endif; ?>
</div>



<?= $form->render() ?>



<script>

$(document).ready(function() {
	handle_deleteConfirmation();
});

</script>


