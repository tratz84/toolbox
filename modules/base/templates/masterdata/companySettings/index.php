

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" onclick="$('form').submit()" class="fa fa-save"></a>
	</div>

	<h1>Bedrijfsgegevens</h1>
</div>


<div class="form-generator">
    <form method="post" action="" enctype="multipart/form-data">
    	<div class="widget text-field-widget">
        	<label><?= t('Companyname') ?></label>
        	<input type="text" name="companyName" value="<?= @esc_attr($settings['companyName']) ?>" />
        </div>
    	<div class="widget text-field-widget">
        	<label><?= t('Street') ?></label>
        	<input type="text" name="companyStreet" value="<?= @esc_attr($settings['companyStreet']) ?>" />
        </div>
        <div class="widget text-field-widget">
        	<label><?= t('Zipcode') ?></label>
        	<input type="text" name="companyZipcode" value="<?= @esc_attr($settings['companyZipcode']) ?>" />
        </div>
        <div class="widget text-field-widget">
        	<label><?= t('City') ?></label>
        	<input type="text" name="companyCity" value="<?= @esc_attr($settings['companyCity']) ?>" />
        </div>
        
        <div class="widget text-field-widget">
        	<label><?= t('Phonenumber') ?></label>
        	<input type="text" name="companyPhone" value="<?= @esc_attr($settings['companyPhone']) ?>" />
        </div>
        
        <div class="widget text-field-widget">
        	<label><?= t('Email') ?></label>
        	<input type="text" name="companyEmail" value="<?= @esc_attr($settings['companyEmail']) ?>" />
        </div>
        
        <div class="widget text-field-widget">
        	<label><?= t('Coc number') ?></label>
        	<input type="text" name="companyCocNumber" value="<?= @esc_attr($settings['companyCocNumber']) ?>" />
        </div>
        
        <div class="widget text-field-widget">
        	<label><?= t('VAT number') ?></label>
        	<input type="text" name="companyVat" value="<?= @esc_attr($settings['companyVat']) ?>" />
        </div>
    
        <div class="widget text-field-widget">
        	<label>IBAN</label>
        	<input type="text" name="companyIBAN" value="<?= @esc_attr($settings['companyIBAN']) ?>" />
        </div>
    
    	<div class="widget text-field-widget">
        	<label><?= t('Prefix numbers') ?> <?= infopopup(t('Prefix for offers, invoices and more for department recognition')) ?></label>
        	<input type="text" name="prefixNumbers" value="<?= @esc_attr($settings['prefixNumbers']) ?>" />
        </div>
        
        <div class="widget text-field-widget">
            <label>Logo</label>
            <input type="file" name="logoFile" />
            
            <?php if (isset($settings['logoFile'])) : ?>
                <img src="<?= url_data_file($settings['logoFile']) ?>" style="max-width: 200px;" />	
            <?php endif; ?>
        </div>
        
        
        
    
    </form>
</div>

