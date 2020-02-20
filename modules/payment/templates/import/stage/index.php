
<script src="<?= appUrl('/module/payment/js/payment-import.js') ?>?v=<?= filemtime(module_file('payment', 'public/js/payment-import.js')) ?>"></script>
<script src="<?= appUrl('/module/payment/js/payment-automatch.js') ?>?v=<?= filemtime(module_file('payment', 'public/js/payment-automatch.js')) ?>"></script>

<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=payment&c=import&a=delete&id='.$pi->getPaymentImportId()) ?>" class="fa fa-trash delete"></a>
		<a href="<?= appUrl('/?m=payment&c=import') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Import</h1>
</div>

<input type="button" id="btnAutomatch" value="Automatch" /> <span id="automatch-status"></span>

<hr/>

<div id="pi-table"></div>

<script>

var pil_data = <?= json_encode($lines) ?>;
var pit;

var paymentAutomatch;


$(document).ready(function() {
	handle_deleteConfirmation();


	pit = new PaymentImportTable('#pi-table');
	pit.setData( pil_data );
	pit.render();

	paymentAutomatch = new PaymentAutomatch();
	paymentAutomatch.setCallbackMatch(function(data) {
		if (data.success) {
			for(var i in data.payment_import_lines) {
    			pit.updateLine( data.payment_import_lines[i] );
    		}
		}

		if (paymentAutomatch.autoNext) {
			var total = $('.payment-import-table .tbody-lines tr').length;
			$('#automatch-status').text( (paymentAutomatch.pos+1) + ' / ' + total );
		}
	}.bind(this));
	paymentAutomatch.setCallbackDone(function() {
		$('#btnAutomatch').prop('disabled', false);
		$('#automatch-status').text( '' );
		showAlert('Done', 'Auto-match done');
	});

	$('#btnAutomatch').click(function() {
		$(this).prop('disabled', true);

// 		paymentAutomatch.matchPayment(1);
		paymentAutomatch.matchAll();
	});
});




</script>

