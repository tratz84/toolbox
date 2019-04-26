

<div class="widget-title">
	Recent aangemaakte offertes
</div>

<table class="list-widget" style="width: 100%;">
	<thead>
		<tr>
			<th>Nr</th>
			<th>Naam</th>
			<th>Omschrijving</th>
			<th>Status</th>
			<th>Aangemaakt op</th>
		</tr>
	</thead>
	<tbody>
    	<?php foreach($offers->getObjects() as $o) : ?>
    	<tr onclick="window.location=appUrl('/?m=invoice&c=offer&a=edit&id=<?= $o['offer_id'] ?>')" class="clickable">
    		<td style="padding-right: 10px;">
    			<?= esc_html( $o['offerNumberText'] ) ?>
    		</td>
    		<td>
    			<?= esc_html($o['company_name']) ?>
    			<?= esc_html($o['lastname']) ?>
    			<?= $o['insert_lastname']?', '.esc_html($o['insert_lastname']):'' ?>
    			<?= esc_html($o['firstname']) ?>
    		</td>
    		<td><?= esc_html($o['subject']) ?></td>
    		<td><?= esc_html($o['offer_status_description']) ?></td>
    		<td><?= date('d-m-Y H:i:s', date2unix($o['created'])) ?></td>
    	</tr>
    	<?php endforeach; ?>
        <?php if (count($offers->getObjects())==0) : ?>
        <tr>
        	<td colspan="4" class="no-records">Geen offertes aangemaakt</td>
        </tr>
        <?php endif; ?>
    </tbody>

</table>
