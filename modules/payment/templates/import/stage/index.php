
<script src="<?= appUrl('/?mpf=/module/payment/js/payment-import.js') ?>?v=<?= filemtime(module_file('payment', 'public/js/payment-import.js')) ?>"></script>
<script src="<?= appUrl('/?mpf=/module/payment/js/payment-automatch.js') ?>?v=<?= filemtime(module_file('payment', 'public/js/payment-automatch.js')) ?>"></script>

<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=payment&c=import&a=delete&id='.$pi->getPaymentImportId()) ?>" class="fa fa-trash delete"></a>
		<a href="<?= appUrl('/?m=payment&c=import') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Import</h1>
</div>

<?php if ($pi->getStatus() != 'done') : ?>
    <input type="button" id="btnAutomatch" value="Automatch" /> <span id="automatch-status"></span>
    <input type="button" id="btnDone" value="Done" title="Mark batch as done" />
<?php elseif ($pi->getStatus() == 'done') : ?>
	<input type="button" id="btnReopen" value="Re-open" title="Re-open batch" />
<?php endif; ?>
    <hr/>
    <div class="filter-container">
    	<label>
    		<input type="checkbox" name="incoming" value="1" <?= get_var('incoming') ? 'checked=checked' : '' ?> />
    		Alleen inkomende bedragen
    	</label>
	</div>
<hr/>

<div id="pi-table"></div>

<script>

var pil_data = <?= json_encode($lines) ?>;
var pit;

var paymentAutomatch;

var pi_status = <?= json_encode($pi->getStatus()) ?>;


$(document).ready(function() {
	handle_deleteConfirmation();
	
	
	pit = new PaymentImportTable('#pi-table', { payment_import_status: pi_status });
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


	$('.filter-container input').change(function() {
		var obj = serialize2object('.filter-container');

		var u = '/?m=payment&c=import/stage&id=<?= $pi->getPaymentImportId() ?>';
		for(var i in obj) {
			u = u + '&'+i+'='+obj[i];
		}

		window.location = appUrl(u);
	});


	$('#btnDone').click(function() {
		window.location = appUrl('/?m=payment&c=import/stage&a=done&id=<?= $pi->getPaymentImportId() ?>');
	});

	$('#btnReopen').click(function() {
		window.location = appUrl('/?m=payment&c=import/stage&a=reopen&id=<?= $pi->getPaymentImportId() ?>');
	});
});




</script>

