
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=invoice&c=invoice') ?>" class="fa fa-chevron-circle-left"></a>
		<?php if ($isNew == false) : ?>
			<?php if (hasCapability('webmail', 'send-mail')) : ?>
				<a href="javascript:void(0);" onclick="sendMail_Click();" class="fa fa-send"></a>
			<?php endif; ?>
			<a href="javascript:void(0);" onclick="print_Click();" class="fa fa-print"></a>
		<?php endif; ?>
		<?php if (isset($errorMessage) == false && $form->isObjectLocked() == false) : ?>
			<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
		<?php endif; ?>
	</div>

    <?php if ($isNew) : ?>
    <h1><?= strOrder(1) ?> toevoegen</h1>
    <?php else : ?>
    <h1><?= strOrder(1) ?> bewerken</h1>
    <?php endif; ?>
</div>


<?= $actionContainer->render() ?>


<?php if (isset($errorMessage)) : ?>
    <div class="error">
    	<?php print esc_html($errorMessage) ?>
    </div>
<?php else : ?>


	<div id="invoice-customer">

	</div>

	<?php print $form->render() ?>

	<?php
        $tabContainer = generate_tabs('invoice', 'invoice-edit-footer', $form);
        if ($form->getWidgetValue('invoice_id'))
            $tabContainer->AddTab(t('Log'), get_component('base', 'activityOverview', 'index', array('stretchtobottom' => false, 'ref_object' => 'invoice__invoice', 'ref_id' => $form->getWidgetValue('invoice_id'))));
        
        if ($tabContainer->hasTabs()) {
            print '<hr/>';
            print $tabContainer->render();
        }
    ?>
	
<?php endif; ?>


