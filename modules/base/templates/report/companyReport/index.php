


<table class="tbl-report">

	<thead>
		<tr>
			<th>Id</th>
			<th>Bedrijfsnaam</th>
			<th>Kvk nr</th>
			<th>Btw nr</th>
			<th>IBAN</th>
			<th>BIC</th>
			<th>Laatst bewerkt</th>
			<th>Aangemaakt op</th>
		</tr>
	</thead>

	<tbody>
		<?php foreach($companies as $c) : ?>
    	<tr class="clickable" data-company-id=" <?= $c->getCompanyId() ?>">
    		<td><?= esc_html($c->getCompanyId()) ?></td>
    		<td><?= esc_html($c->getCompanyName()) ?></td>
    		<td><?= esc_html($c->getCocNumber()) ?></td>
    		<td><?= esc_html($c->getVatNumber()) ?></td>
    		<td><?= esc_html($c->getIban()) ?></td>
    		<td><?= esc_html($c->getBic()) ?></td>
    		<td><?= esc_html($c->getEditedFormat('d-m-Y H:i:s')) ?></td>
    		<td><?= esc_html($c->getCreatedFormat('d-m-Y H:i:s')) ?></td>
    	</tr>
    	<?php endforeach; ?>
    </tbody>

</table>

<script>
$(document).ready(function() {
	$('.tbl-report tbody tr').each(function(index, node) {
		$(node).click(function() {
			window.open( appUrl('/?m=base&c=company&a=edit&company_id=' + $(node).data('company-id')) );
		});
	});
});

</script>

