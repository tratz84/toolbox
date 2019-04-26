
<div>
    
    <table class="list-widget">
    	<thead>
    		<tr>
    			<th>Offerte nr</th>
    			<th>Betreft</th>
    			<th style="text-align: right; padding-right: 2em;">Bedrag</th>
    			<th>Status</th>
    			<th>Aangemaakt op</th>
    			<th></th>
    		</tr>
    	</thead>
    	
    	<tbody>
    		<?php foreach($listResponse->getObjects() as $o) : ?>
    		<tr class="clickable" onclick="offerOverviewRow_Click(window.event, <?= $o['offer_id'] ?>);">
    			<td><?= esc_html($o['offerNumberText']) ?></td>
    			<td><?= esc_html($o['subject']) ?></td>
    			<td style="text-align: right; padding-right: 2em;">
    				<?php if ($invoiceSettings->getPricesIncVat()) : ?>
    					<?= format_price($o['total_calculated_price_incl_vat'], true, ['thousands' => '.']) ?>
    				<?php else : ?>
    					<?= format_price($o['total_calculated_price'], true, ['thousands' => '.']) ?>
    				<?php endif; ?>
    			</td>
    			<td><?= esc_html($o['offer_status_description']) ?>
    			<td><?= format_date($o['created']) ?></td>
    			<td class="td-offer">
    				<a href="<?= appUrl('/?m=invoice&c=offer&a=print&id='.$o['offer_id']) ?>" class="fa fa-print" target="_blank"></a>
    			</td>
    		</tr>
    		<?php endforeach; ?>
    	</tbody>
    
    
    </table>

</div>


<script>
function offerOverviewRow_Click(evt, offerId) {
	if ($(evt.target).hasClass('td-offer') || $(evt.target).closest('.td-offer').length) {
		return;
	}

	window.location = appUrl('/?m=invoice&c=offer&a=edit&id=' + offerId);
}
</script>
