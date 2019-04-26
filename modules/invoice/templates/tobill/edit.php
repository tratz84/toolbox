
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=invoice&c=tobill') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1>Billable toevoegen</h1>
    <?php else : ?>
    <h1>Billable bewerken</h1>
    <?php endif; ?>
</div>


<?php
    $tabContainer = generate_tabs('invoice', 'tobill-edit', $form);
    $tabContainer->addTab('Billable', $form->render(), 0);
    print $tabContainer->render();
?>
