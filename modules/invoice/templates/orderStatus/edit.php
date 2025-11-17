
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=invoice&c=orderStatus') ?>" class="fa fa-chevron-circle-left"></a>
		<?php if ($isNew == false) : ?>
		<a href="javascript:void();" onclick="delete_orderStatus();" class="fa fa-trash delete"></a>
		<?php endif; ?>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1>Order status toevoegen</h1>
    <?php else : ?>
    <h1>Order status bewerken</h1>
    <?php endif; ?>
</div>


<?php print $form->render() ?>



<script>

function delete_orderStatus() {
	showConfirmation( t('Delete order status'), t('Are you sure to delete this order status?'), () => {
		window.location = appUrl('/?m=invoice&c=orderStatus&a=delete&id=' + $('input[name=order_status_id]').val());
	});
}


</script>

