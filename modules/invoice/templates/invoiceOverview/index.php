
<div>
    
    <table class="list-widget">
    	<thead>
    		<tr>
    			<th><?= strOrder(1) ?> nr</th>
    			<th>Betreft</th>
    			<th style="text-align: right; padding-right: 2em;">Bedrag</th>
    			<th>Status</th>
    			<th><?= strOrder(1) ?>datum</th>
    			<th></th>
    		</tr>
    	</thead>
    	
    	<tbody>
    		<?php foreach($listResponse->getObjects() as $o) : ?>
    		<tr class="clickable" onclick="invoiceOverviewRow_Click(window.event, <?= $o['invoice_id'] ?>);">
    			<td><?= esc_html($o['invoiceNumberText']) ?></td>
    			<td><?= esc_html($o['subject']) ?></td>
    			<td style="text-align: right; padding-right: 2em;">
    				<?php if ($invoiceSettings->getPricesIncVat()) : ?>
    					<?= format_price($o['total_calculated_price_incl_vat'], true, ['thousands' => '.']) ?>
    				<?php else : ?>
    					<?= format_price($o['total_calculated_price'], true, ['thousands' => '.']) ?>
    				<?php endif; ?>
    			</td>
    			<td><?= esc_html($o['invoice_status_description']) ?></td>
    			<td><?= format_date($o['invoice_date']) ?></td>
    			<td class="td-invoice">
    				<a href="<?= appUrl('/?m=invoice&c=invoice&a=print&id='.$o['invoice_id']) ?>" class="fa fa-print" target="_blank"></a>
    			</td>
    		</tr>
    		<?php endforeach; ?>
    	</tbody>
    	<tbody>
    	<?php if ($listResponse->getRowCount() == 0) : ?>
    	<tr>
    		<td colspan="6" style="text-align: center; font-style: italic;">Geen <?= strtolower(strOrder(2)) ?> aangemaakt</td>
    	</tr>
    	<?php endif; ?>
    	</tbody>
    	
    
    
    </table>

</div>

<script>
function invoiceOverviewRow_Click(evt, invoiceId) {
	if ($(evt.target).hasClass('td-invoice') || $(evt.target).closest('.td-invoice').length) {
		return;
	}
	
	window.location = appUrl('/?m=invoice&c=invoice&a=edit&id=' + invoiceId);
}
</script>
