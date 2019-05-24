
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=person') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1><?= $t('Add person') ?></h1>
    <?php else : ?>
    <h1><?= esc_html(format_personname($person)) ?></h1>
    <?php endif; ?>
</div>

<?= $actionContainer->render() ?>

<?php
    $tabContainer = generate_tabs('base', 'person-edit-footer', $form);
    $tabContainer->addTab(t('Personal data'), $form->render(), 0);
    print $tabContainer->render();
?>


<br/>
