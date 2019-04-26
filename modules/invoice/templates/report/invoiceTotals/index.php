

<?= $form->render() ?>


<hr/>

<table class="list-widget">
	<thead>
    	<tr>
    		<th>Klant</th>
    		<th class="right">Totaalbedrag</th>
    		<th class="right">Aantal <?= strtolower(strOrder(2)) ?></th>
    		<th class="right">Gemiddeld <?= strtolower(strOrder(1)) ?>bedrag</th>
    	</tr>
    </thead>
	<tbody>
		<?php $totalBilled = 0; ?>
		<?php $totalInvoices = 0; ?>
		<?php foreach($totals as $r) : ?>
    		<?php $totalBilled += $r['total_billed'] ?>
    		<?php $totalInvoices += $r['number_invoices'] ?>
    		<tr class="clickable" onclick="record_Click(this);" data-company-id="<?= $r['company_id'] ?>" data-person-id="<?= $r['person_id'] ?>">
    			<td>
    				<?php if ($r['company_name']) : ?>
    					<?= esc_html($r['company_name']) ?>
    				<?php else : ?>
    					<?= esc_html(format_personname($r)) ?>
    				<?php endif; ?>
    			</td>
    			<td class="right">
    				<?= format_price($r['total_billed'], true, ['thousands' => '.']) ?>
    			</td>
    			<td class="right">
    				<?= $r['number_invoices'] ?>
    			</td>
    			<td class="right">
    				<?= format_price($r['total_billed'] / $r['number_invoices'], true, ['thousands' => '.']) ?>
    			</td>
    		</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot style="font-weight: bold;">
		<tr>
			<td></td>
			<td class="right"><?= format_price($totalBilled, true, ['thousands' => '.']) ?></td>
			<td class="right"><?= $totalInvoices ?></td>
			<td class="right"><?= format_price($totalInvoices?$totalBilled/$totalInvoices:0, true, ['thousands' => '.']) ?></td>
		</tr>
	</tfoot>

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
		window.open(appUrl('/?m=base&c=company&a=edit&company_id=' + $(tr).data('company-id'), '_blank'));
	}
	if ($(tr).data('person-id')) {
		window.open(appUrl('/?m=base&c=person&a=edit&person_id=' + $(tr).data('person-id'), '_blank'));
	}
	
}

</script>



