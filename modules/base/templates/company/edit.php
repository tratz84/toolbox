

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=company') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1>Bedrijf toevoegen: <span id="h1-company-name"></span></h1>
    <?php else : ?>
    <h1><span id="h1-company-name"><?= esc_html($form->getWidgetValue('company_name')) ?></span></h1>
    <?php endif; ?>
</div>

<?= $actionContainer->render() ?>


<?php
    $tabContainer = generate_tabs('base', 'company-edit-footer', $form);
    $tabContainer->addTab('Bedrijfsgegevens', $form->render(), 0);
    print $tabContainer->render();
?>

<br/>


<script>

$(document).ready(function() {
	link_input2text('[name=company_name]', '#h1-company-name');


	$('[name=vat_number]').change(function() {
		var v = $.trim(this.value);
		if (v == '') {
			$(this).css('border', '');
			return;
		}
		
		$.ajax({
			url: appUrl('/?m=base&c=company'),
			data: {
				a: 'check_vat_number',
				vat_number: this.value
			},
			success: function(xhr, data, textStatus) {
				if (xhr.success) {
					$('[name=vat_number]').css('border', '');
				} else {
					$('[name=vat_number]').css('border', '1px solid #f00');
					showInlineWarning('Let op, btw-nummer verificatie mislukt', { timeout: 2000 });
				}
			}
		});
	});
});


</script>

