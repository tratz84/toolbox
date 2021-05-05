

<div class="widget-title">
	Recent gewijzigde offertes
</div>

<table class="list-widget" style="width: 100%;">
	<thead>
		<tr>
			<th class="th-name-description">Naam / Betreft</th>
			<th class="th-name">Naam</th>
			<th class="th-subject">Betreft</th>
			<th class="th-status">Status</th>
			<th class="th-edited">Gewijzigd op</th>
		</tr>
	</thead>
	<tbody>
    	<?php foreach($offers->getObjects() as $o) : ?>
    	<tr onclick="window.location=appUrl('/?m=invoice&c=offer&a=edit&id=<?= $o['offer_id'] ?>')" class="clickable">
    		<td class="td-name-description">
    			<div class="customer-name"><?= esc_html(format_customername($o)) ?></div>
    			<div class="description"><?= esc_html($o['subject']) ?></div>
    		</td>
    		<td style="padding-left: 5px;" class="td-name"><?= esc_html(format_customername($o)) ?></td>
    		<td class="td-subject"><?= esc_html($o['subject']) ?></td>
    		<td class="td-status"><?= esc_html($o['offer_status_description']) ?></td>
    		<td class="td-edited"><?= date('d-m-Y H:i:s', date2unix($o['edited'])) ?></td>
    	</tr>
    	<?php endforeach; ?>
        <?php if (count($offers->getObjects())==0) : ?>
        <tr>
        	<td colspan="4" class="no-results-found">Geen offertes aangemaakt</td>
        </tr>
        <?php endif; ?>
    </tbody>

</table>
