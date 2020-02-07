
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=payment&c=import&a=delete&id='.$pi->getPaymentImportId()) ?>" class="fa fa-trash delete"></a>
		<a href="<?= appUrl('/?m=payment&c=import') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Import</h1>
</div>



<script>

$(document).ready(function() {
	handle_deleteConfirmation();
});

</script>

