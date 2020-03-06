

<div class="widget-title">
	<div class="toolbox">
		<a href="javascript:void(0);" onclick="show_popup(appUrl('/?m=invoice&c=dashboardWidgets&a=lastInvoices_settings'));" class="fa fa-cog"></a>
	</div>

	<?= t('Recent') ?> <?= strtolower(strOrder(2)) ?>
</div>

<table class="list-widget" style="width: 100%;">
	<thead>
		<tr>
			<th><?= t('Number_short') ?></th>
			<th><?= t('Name') ?></th>
			<th><?= t('Description') ?></th>
			<?php if ($widgetSettings['show_invoice_amount']) : ?>
			<th style="text-align: right; padding-right: 10px;">
				Bedrag
				
				<?php if ($invoiceSettings->getPricesIncVat()) : ?>
					<?= infopopup('Bedragen inclusief btw') ?>
				<?php else : ?>
					<?= infopopup('Bedragen exclusief btw') ?>
				<?php endif; ?>
			</th>
			<?php endif; ?>
			<th><?= t('State') ?></th>
			<?php if ($widgetSettings['show_open_days']) : ?>
			<th>Dagen</th>
			<?php endif; ?>
			<th><?= strOrder(1) ?><?= t('date') ?></th>
		</tr>
	</thead>
	<tbody>
    	<?php foreach($invoices->getObjects() as $i) : ?>
    	<tr onclick="window.location=appUrl('/?m=invoice&c=invoice&a=edit&id=<?= $i['invoice_id'] ?>')" class="clickable">
    		<td style="padding-right: 10px;">
    			<?= esc_html( $i['invoiceNumberText'] ) ?>
    		</td>
    		<td>
    			<?= esc_html($i['company_name']) ?>
    			<?= esc_html(format_personname($i)) ?>
    		</td>
    		<td><?= esc_html($i['subject']) ?></td>
    		<?php if ($widgetSettings['show_invoice_amount']) : ?>
			<td style="text-align: right; padding-right: 10px;">
				<?php if ($invoiceSettings->getPricesIncVat()) : ?>
					<?= format_price($i['total_calculated_price_incl_vat'], true, ['thousands' => '.']) ?>
				<?php else : ?>
					<?= format_price($i['total_calculated_price'], true, ['thousands' => '.']) ?>
				<?php endif; ?>
			</td>
			<?php endif; ?>
    		<td><?= esc_html($i['invoice_status_description']) ?></td>
    		<?php if ($widgetSettings['show_open_days']) : ?>
			<td><?= days_between($i['invoice_date'], date('Y-m-d')) ?></td>
			<?php endif; ?>
    		<td><?= format_date($i['invoice_date'], 'd-m-Y') ?></td>
    	</tr>
    	<?php endforeach; ?>
        <?php if (count($invoices->getObjects())==0) : ?>
        <tr>
        	<td colspan="100" class="no-records"><?= t('No invoices found') ?></td>
        </tr>
        <?php endif; ?>
    </tbody>

</table>
