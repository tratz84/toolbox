
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=payment&c=paymentOverview') ?>" class="fa fa-chevron-circle-left"></a>
		
		<?php if (hasCapability('payment', 'edit-payments')) : ?>
    		<?php if ($isNew == false) : ?>
    		<a href="<?= appUrl('/?m=payment&c=payment&a=delete&id='.$paymentId) ?>" class="fa fa-trash delete"></a>
    		<?php endif; ?>
    		
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




<script>

var isNew = <?= json_encode($isNew) ?>;
var is_get_request = <?= json_encode(is_get()) ?>;

$(document).ready(function() {
	if (isNew && is_get_request) {
		$('.add-record').click();
	}
});


</script>


