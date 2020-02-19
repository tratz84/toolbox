<?php
use base\forms\CustomerSelectWidget;
?>

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
			<th></th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach($pi->getImportLines() as $pl) : ?>
		<?php $plid = $pl->getPaymentImportLineId(); ?>
		<tr data-payment-import-line-id="<?= $plid ?>" id="line-<?= $plid ?>">
			<td>
				Status
			</td>
			<td>
				<div class="customer-selection" data-company-id="<?= $pl->getCompanyId() ?>" data-person-id="<?= $pl->getPersonId() ?>">
				<?= format_customername($pl) ?>
				</div>
			</td>
			<td>
				<?php
				if ($pl->getInvoiceId()) print $prefixNumbers . $pl->getInvoiceId();
				?>
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
			<td>
				<input type="button" value="Import" />
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

</table>


<script>

$(document).ready(function() {
	handle_deleteConfirmation();


	$('.customer-selection').click(function() {
		var tr = $(this).closest('tr');
		var plid = $(tr).data('payment-import-line-id');

		handle_customerSelection( plid );
	});
});


function handle_customerSelection(plid) {
	var row = $('#line-'+plid);

	if (row.find('.select-user').length > 0) {
		return;
	}

	var customer_name = row.find('.customer-selection').text();
	var person_id = row.find('.customer-selection').data('person-id');
	var company_id = row.find('.customer-selection').data('company-id');

	console.log(company_id);

	// remove old text
	row.find('.customer-selection').empty();

	// add select2-box
	var s = $('<select class="select-user" name="customer_id_'+plid+'"></select>');
	var opt = $('<option value=""></option>');
	opt.text( customer_name );
	if (company_id) {
		opt.attr('value', 'company-'+company_id);
	} else if (person_id) {
		opt.attr('value', 'person-'+person_id);
	} else {
		opt.text('Maak uw keuze');
	}
	s.append(opt);
	row.find('.customer-selection').append( s );

	// init select2
	$(s).select2({
		ajax: {
    		url: appUrl('/?m=base&c=customer&a=select2'),
    		type: 'POST',
    		data: function(params) {
				var d = {};

        		d.name = params.term;
        		
        		return d;
    		}
		}
	});

	// handle customer selection
	$(s).on("select2:select", function (e) {
		var v = $(this).val();
		var plid = $(this).closest('tr').data('payment-import-line-id');
		
		if (v != '') {
			set_customer( plid, v );
		}
	});
}

function set_customer(plid, customer_id) {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=payment&c=import/stage&a=update_customer'),
		data: {
			payment_line_import_id: plid,
			customer_id: customer_id
		},
		success: function(data, xhr, textStatus) {
			if (data.success) {
				var cs = $('#line-'+plid).find('.customer-selection');
				cs.text( data.name );
				cs.data('person-id', data.person_id);
				cs.data('company-id', data.company_id);
			}
		}
	});
}




</script>

