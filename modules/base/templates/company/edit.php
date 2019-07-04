

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=company') ?>" class="fa fa-chevron-circle-left"></a>
		
		<?= render_object_log_button('company', $company_id) ?>
		
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1>Bedrijf toevoegen</h1>
    <?php else : ?>
    <h1><?= esc_html($form->getWidgetValue('company_name')) ?></h1>
    <?php endif; ?>
</div>

<?= $actionContainer->render() ?>


<?php
    $tabContainer = generate_tabs('base', 'company-edit-footer', $form);
    $tabContainer->addTab('Bedrijfsgegevens', $form->render(), 0);
    print $tabContainer->render();
?>

<br/>
