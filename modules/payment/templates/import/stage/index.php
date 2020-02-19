
<script src="<?= appUrl('/module/payment/js/payment-import.js') ?>?v=<?= filemtime(module_file('payment', 'public/js/payment-import.js')) ?>"></script>

<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=payment&c=import&a=delete&id='.$pi->getPaymentImportId()) ?>" class="fa fa-trash delete"></a>
		<a href="<?= appUrl('/?m=payment&c=import') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Import</h1>
</div>


<div id="pi-table"></div>

<script>

var pil_data = <?= json_encode($lines) ?>;
var pit;

$(document).ready(function() {
	handle_deleteConfirmation();


	pit = new PaymentImportTable('#pi-table');
	pit.setData( pil_data );
	pit.render();
});




</script>

