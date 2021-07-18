

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=customer&c=company') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1><?= t('Add company') ?>: <span id="h1-company-name"></span></h1>
    <?php else : ?>
    <h1><span id="h1-company-name"><?= esc_html($form->getWidgetValue('company_name')) ?></span></h1>
    <?php endif; ?>
</div>

<?= $actionContainer->render() ?>


<?php
    $tabContainer = generate_tabs('customer', 'company-edit-footer', $form);
    $tabContainer->addTab(t('Company settings'), $form->render(), 0);
    print $tabContainer->render();
?>

<br/>


<script>

$(document).ready(function() {
	link_input2text('[name=company_name]', '#h1-company-name');

});


</script>

