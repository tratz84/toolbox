
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=invoice&c=offer') ?>" class="fa fa-chevron-circle-left"></a>
		<?php if ($isNew == false) : ?>
			<?php if (hasCapability('webmail', 'send-mail')) : ?>
				<a href="javascript:void(0);" onclick="sendMail_Click();" class="fa fa-send"></a>
			<?php endif; ?>
			<a href="javascript:void(0);" onclick="print_Click();" class="fa fa-print"></a>
		<?php endif; ?>
		
		<?= render_dbobject_lock($offer, 'Offerte '.$offer->getOfferNumberText()) ?>
		
		<?php if (isset($errorMessage) == false && dbobject_is_locked($offer) == false) : ?>
			<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
		<?php endif; ?>
	</div>

    <?php if ($isNew) : ?>
    <h1>Offerte toevoegen</h1>
    <?php else : ?>
    <h1>Offerte bewerken</h1>
    <?php endif; ?>
</div>

<?php if ($actionContainer->hasItems()) : ?>
	<div class="action-box">
	<?php $items = $actionContainer->getItems(); ?>
	<?php for($x=0; $x < count($items); $x++) : ?>
		<span><?php print $items[$x]['html']; ?></span>
	<?php endfor; ?>
	</div>
	<hr/>
<?php endif; ?>


<?php if (isset($errorMessage)) : ?>
    <div class="error">
    	<?php print esc_html($errorMessage) ?>
    </div>
<?php else : ?>


	<div id="offer-customer">

	</div>

	<input type="hidden" id="invoice_id" value="<?= esc_attr($invoiceId) ?>" />

	<?php print $form->render() ?>

	<?php
        $tabContainer = generate_tabs('invoice', 'offer-edit-tabs', $form);
        if ($form->getWidgetValue('offer_id'))
            $tabContainer->AddTab(t('Log'), get_component('base', 'activityOverview', 'index', array('stretchtobottom' => false, 'ref_object' => 'invoice__offer', 'ref_id' => $form->getWidgetValue('offer_id'))));
        
        if ($tabContainer->hasTabs()) {
            print '<hr/>';
            print $tabContainer->render();
        }
    ?>

<?php endif; ?>


