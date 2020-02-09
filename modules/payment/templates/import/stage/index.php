
<div class="page-header">

	<div class="toolbox">
		<a href="<?= appUrl('/?m=payment&c=import&a=delete&id='.$pi->getPaymentImportId()) ?>" class="fa fa-trash delete"></a>
		<a href="<?= appUrl('/?m=payment&c=import') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Import</h1>
</div>



<table class="list-response-table payment-import-table">
	<thead>
		<tr>
			<th>Status</th>
			<th>Customer</th>
			<th>Invoice</th>
			<th>Bankaccounts</th>
			<th class="amount">Amount</th>
			<th>Name</th>
			<th>Description</th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach($pi->getImportLines() as $pl) : ?>
		<tr>
			<td>
				Status
			</td>
			<td>
			 TODO, cust..
			</td>
			<td>
				Invoice..
			</td>
			
			<td>
				<?= esc_html($pl->getBankaccountno()) ?>
				<br/>
				<?= esc_html($pl->getBankaccountnoContra()) ?>
			</td>
			<td class="amount"><?= format_price($pl->getAmount()) ?></td>
			
			<td title="<?= esc_attr($pl->getName()) ?>">
				<?= esc_html(limit_text($pl->getName(), 50)) ?>
			</td>
			
			<td title="<?= esc_attr($pl->getDescription()) ?>">
				<?= esc_html(limit_text($pl->getDescription(), 50)) ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

</table>


<script>

$(document).ready(function() {
	handle_deleteConfirmation();
});

</script>

