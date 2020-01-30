
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=payment&c=paymentOverview') ?>" class="fa fa-chevron-circle-left"></a>
		
		<?php if (hasCapability('payment', 'edit-payments')) : ?>
    		<?php if ($isNew == false) : ?>
    		<a href="<?= appUrl('/?m=payment&c=payment&a=delete&id='.$paymentId) ?>" class="fa fa-trash delete"></a>
    		
    		<a href="javascript:void(0);" onclick="print_Click();" class="fa fa-print" target="_blank"></a>
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


	$('.payment-form-payment-line-list-edit').get(0).lefw.setCallbackAddRecord(function(row) {
		payment_calc_totals();
	});
	
	$('.payment-form-payment-line-list-edit').get(0).lefw.setCallbackDeleteRecord(function(row) {
		payment_calc_totals();
	});
	

	payment_calc_totals();
});


function print_Click() {
	var frm = $('.form-payment-form');
	var data = serialize2object( frm );

	formpost('/?m=payment&c=payment&print=1&id=' + $('[name=payment_id]').val(), data, { target: '_blank' });
}


function payment_calc_totals() {
	console.log('calc totals');
	$('.payment-form-payment-line-list-edit').find('tr td.input-amount input[type=text]').change(function() { payment_calc_totals(); });
	
	var totalAmount = 0;
	
	$('.payment-form-payment-line-list-edit tbody tr').each(function(index, row) {
		if ($(row).find('.input-amount').length == 0)
			return;
		
		var amount = strtodouble( $(row).find('.input-amount input[type=text]').val() );
		totalAmount += amount;
	});
	
	var tfoot = $('.payment-form-payment-line-list-edit tfoot');
	tfoot.empty();
	
	var trTotalAmount = $('<tr><td colspan="3"></td><td class="td-foot-amount" style="padding-left: 5px;"></td></tr>');
	trTotalAmount.find('.td-foot-amount').text( format_price(totalAmount, true) );
	tfoot.append( trTotalAmount );
}

</script>


