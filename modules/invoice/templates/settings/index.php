<?php
use invoice\pdf\LandscapeOfferPdf;
use invoice\pdf\DefaultOfferPdf;
use invoice\pdf\DefaultInvoicePdf;
?>


<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" onclick="$('form').submit()" class="fa fa-save"></a>
	</div>

	<h1>Facturatie instellingen</h1>
</div>



<div class="form-generator">
    <form method="post" action="" enctype="multipart/form-data">
    	
        <div class="widget text-field-widget">
        	<label>
        		Betalingsbevestiging
        		<?= infopopup('Maakt het systeem orders of facturen aan?') ?>
    		</label>
        	
        	<div style="float: left; margin-bottom: 5px;">
            	<?= render_radio('invoice__orderType', 'invoice', ['checked' => $settings['invoice__orderType'] == 'invoice']) ?>
            	<label for="invoice__orderType-invoice" style="float: none; width: auto;">Factuur</label>
            	<br/>
            	<?= render_radio('invoice__orderType', 'order', ['checked' => $settings['invoice__orderType'] == 'order']) ?>
            	<label for="invoice__orderType-order" style="float: none; width: auto;">Order</label>
        	</div>
        </div>
		
		<div class="widget text-field-widget">
        	<label>
        		Offerte template
    		</label>
        	
        	<select name="invoice__offerTemplate">
        		<?php foreach($invoiceSettings->getOfferPdfTemplates() as $clazz => $templateDescription) : ?>
        		<option value="<?= esc_attr($clazz) ?>" <?= @$settings['invoice__offerTemplate'] == $clazz ? 'selected=selected' : '' ?>><?= esc_html($templateDescription) ?></option>
        		<?php endforeach; ?>
        	</select>
        	
        	<a href="<?= appUrl('/?m=invoice&c=pdfsettings') ?>" class="fa fa-cog"></a>
        </div>
        
        <div class="widget text-field-widget">
        	<label>
        		<?= strOrder(1) ?>template
    		</label>
        	
        	<select name="invoice__invoiceTemplate">
        		<?php foreach($invoiceSettings->getInvoicePdfTemplates() as $clazz => $templateDescription) : ?>
        		<option value="<?= esc_attr($clazz) ?>" <?= @$settings['invoice__invoiceTemplate'] == $clazz ? 'selected=selected' : '' ?>><?= esc_html($templateDescription) ?></option>
        		<?php endforeach; ?>
        	</select>
        </div>
        
        <div class="widget checkbox-field-widget">
        	<label>
        		Intracommunautaire<br/>leveringen
        		
        		<?= infopopup('Voegt "Intracommunautaire" vink toe bij bedrijfsgegevens. Bij het genereren van facturen voor deze bedrijven wordt dan op de factuur gezet dat deze intracommunautair is.') ?>
    		</label>
        	
        	<input type="checkbox" class="checkbox-ui" id="invoice__intracommunautaire" name="invoice__intracommunautaire" <?= $invoiceSettings->getIntracommunautair() ? 'checked="checked"' : '' ?> />
        	<label class="checkbox-ui-placeholder" for="invoice__intracommunautaire"></label>
        </div>
        
        <div class="widget checkbox-field-widget">
        	<label>
        		Bedragen incl. btw tonen
        		
        		<?= infopopup('In de overzichten bedragen incl. of excl. btw tonen?') ?>
    		</label>
        	
        	<input type="checkbox" class="checkbox-ui" id="invoice__prices_inc_vat" name="invoice__prices_inc_vat" <?= $invoiceSettings->getPricesIncVat() ? 'checked="checked"' : '' ?> />
        	<label class="checkbox-ui-placeholder" for="invoice__prices_inc_vat"></label>
        </div>
        
        <br/><br/>

        <div class="widget checkbox-field-widget">
        	<label>
        		Billable
        		<?= infopopup(t('Notes for bills to pay and services/work to invoice')) ?>
    		</label>
        	
        	<input type="checkbox" class="checkbox-ui" 
        			id="invoice__billable_enabled" name="invoice__billable_enabled" 
        			<?= $invoiceSettings->getBillableEnabled() ? 'checked="checked"' : '' ?> />
        	<label class="checkbox-ui-placeholder" for="invoice__billable_enabled"></label>
        </div>
        <div class="billable-options">
            <div class="widget checkbox-field-widget">
            	<label style="padding-left: 5px;">
            		Default 'Not paid' only
            		<?= infopopup('Standaard alleen opstaande betalingen/facturatie records tonen?') ?>
        		</label>
            	
            	<input type="checkbox" class="checkbox-ui" 
            			id="invoice__billable_only_open" name="invoice__billable_only_open" 
            			<?= $invoiceSettings->getBillableOnlyOpen() ? 'checked="checked"' : '' ?> />
            	<label class="checkbox-ui-placeholder" for="invoice__billable_only_open"></label>
            </div>
        </div>
        
		
		<br/><br/><br/>

        <div class="upload-field-widget">
            <br/>
        	<div class="clearfix"></div>
        	<h3 style="float: left;">Bijlages offerte</h3> <?= infopopup('Bestanden welke automatisch bij offerte mails worden gevoegd') ?>
        	<div class="clearfix"></div>
        	<ul>
        		<?php foreach(list_data_files('attachments/offer/') as $f) : ?>
        		<li>
        			<a href="<?= esc_attr(url_data_file('attachments/offer/'.$f)) ?>" target="_blank"><?= esc_html($f) ?></a>
        			
        			<a href="<?= appUrl('/?m=invoice&c=settings&a=delete_offer_file&f='.urlencode($f)) ?>"><span class="fa fa-close remove-file"></span></a>
        		</li>
        		<?php endforeach; ?>
        	</ul>
        	
        	<input type="file" name="fileAttachmentOffer" onchange="this.form.submit();" />
        </div>
        
        <br/>
        
        <div class="upload-field-widget">
            <br/>
        	<div class="clearfix"></div>
        	<h3 style="float: left;">Bijlages <?= strtolower(strOrder(2)) ?></h3> <?= infopopup('Bestanden welke automatisch bij '.strtolower(strOrder(1)).' mails worden gevoegd') ?>
        	<div class="clearfix"></div>
        	<ul>
        		<?php foreach(list_data_files('attachments/invoice/') as $f) : ?>
        		<li>
        			<a href="<?= esc_attr(url_data_file('attachments/invoice/'.$f)) ?>" target="_blank"><?= esc_html($f) ?></a>
        			
        			<a href="<?= appUrl('/?m=invoice&c=settings&a=delete_invoice_file&f='.urlencode($f)) ?>"><span class="fa fa-close remove-file"></span></a>
        		</li>
        		<?php endforeach; ?>
        	</ul>
        	
        	<input type="file" name="fileAttachmentInvoice" onchange="this.form.submit();" />
        </div>
        
    	
    	<br/><br/>

    </form>
</div>



<script>

$(document).ready(function() {
	$('[name=invoice__billable_enabled]').change(function() {
		handleBillableOptions();
	});
	handleBillableOptions();
});
function handleBillableOptions() {
	if ($('[name=invoice__billable_enabled]').prop('checked')) {
		$('.billable-options').show();
	} else {
		$('.billable-options').hide();
	}
}


</script>



