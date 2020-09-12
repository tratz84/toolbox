



<hr/>

<table class="list-widget">
	<thead>
    	<tr>
    		<th>Klant</th>
    		<th class="right">Totaalbedrag excl. btw</th>
    		<th class="right">Totaalbedrag incl. btw</th>
    		<th class="right">Totaal betaald</th>
    		<th class="right">Openstaand bedrag</th>
    	</tr>
    </thead>
	<tbody>
		<?php foreach($customerIds as $cid) : ?>
			<?php $tc = $totalsByCustomer[$cid] ?>
    		<tr class="clickable" onclick="record_Click(this);" data-company-id="<?= $tc['company_id'] ?>" data-person-id="<?= $tc['person_id'] ?>">
    			<td>
    				<?= esc_html(format_customername($tc)) ?>
    			</td>
    			<td class="right">
    				<?= format_price(@$tc['sum_total_calculated_price'], true, ['thousands' => '.']) ?>
    			</td>
    			<td class="right">
    				<?= format_price(@$tc['sum_total_calculated_price_incl_vat'], true, ['thousands' => '.']) ?>
    			</td>
    			<td class="right">
    				<?= format_price(@$tc['total_amount'], true, ['thousands' => '.']) ?>
    			</td>
    			<td class="right" style="<?= $tc['diff_cents'] > 0 ? 'color: #f00;' : '' ?>">
    				<?= format_price(@$tc['open_amount'], true, ['thousands' => '.']) ?>
    			</td>
    		</tr>
		<?php endforeach; ?>
	</tbody>

</table>



<script>

$(document).ready(function() {
	var frm = $('form.form-generator');
	frm.attr('method', 'get');
	frm.find('[type=submit]').hide();


	frm.find('select').change(function() {
		this.form.submit();
	});
	frm.find('.input-pickadate').on('dp.change', function() {
		$(this).data('changed', true);
	});
	frm.find('.input-pickadate').blur(function() {
		if ($(this).data('changed'))
			this.form.submit();
	});
});


function record_Click(tr) {
	if ($(tr).data('company-id')) {
		window.open(appUrl('/?m=customer&c=company&a=edit&company_id=' + $(tr).data('company-id'), '_blank'));
	}
	if ($(tr).data('person-id')) {
		window.open(appUrl('/?m=customer&c=person&a=edit&person_id=' + $(tr).data('person-id'), '_blank'));
	}
	
}

</script>



