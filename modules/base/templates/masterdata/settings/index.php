

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" onclick="$('form').submit()" class="fa fa-save"></a>
	</div>

	<h1>Basisinstellingen</h1>
</div>


<?php ob_start() ?>
    <div class="widget text-field-widget">
    	<label>Page size</label>
    	<input type="number" name="PAGE_SIZE" step="1" min="10" max="100" value="<?= esc_attr($settings['PAGE_SIZE']) ?>" />
    </div>
    
    <div class="list-available-modules">
        <?php foreach($availableModules as $m) : ?>
        <?php $moduleEnabled = @$settings[$m->getTag().'Enabled'] ? true : false; ?>
        <div class="widget text-field-widget module-line module-<?= slugify($m->getName()) ?>" style="<?= $moduleEnabled ? 'background-color: #c6f3ce;' : '' ?>">
        	<label>
        		<?= esc_html($m->getName()) ?>
        		<?= infopopup($m->getInfoText()) ?>
    		</label>
    		
    		<?php if ($moduleEnabled) : ?>
    			<a href="<?= appUrl('/?m=base&c=masterdata/settings&a=deactivate_module&mod='.urlencode($m->getTag())) ?>">Deactiveer</a>
    		<?php else : ?>
    			<a href="<?= appUrl('/?m=base&c=masterdata/settings&a=activate_module&mod='.urlencode($m->getTag())) ?>">Activeer</a>
    		<?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <div class="clear" style="height: 1em;"></div>
<?php $htmlBaseSettings = ob_get_clean(); ?>


<?php ob_start() ?>

	<div style="padding: 5px 5px 15px 5px; margin-bottom: 15px;">
		<?= $checkboxObjectLocking->render() ?>
	</div>

	<div class="clear"></div>
<?php $htmlExtra = ob_get_clean() ?>


<?php ob_start() ?>

        <?= render_colorpicker('master_base_color', 'Basiskleur', @$settings['master_base_color']) ?>

	<div class="clear"></div>
<?php $htmlColors = ob_get_clean() ?>



<div class="form-generator">
    <form method="post" action="">
        <?php 
            $tabContainer = generate_tabs('base', 'masterdata-settings', null);
            $tabContainer->addTab('Basisinstellingen', $htmlBaseSettings, 0);
            $tabContainer->addTab('Extra', $htmlExtra);
            $tabContainer->addTab('Kleuren', $htmlColors);
            print $tabContainer->render();
        ?>
        
    </form>
</div>




