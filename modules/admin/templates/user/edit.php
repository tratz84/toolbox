
<style type="text/css">

.widget-container-insights-customers .text-field-widget { width: 215px; clear: both; }
.widget-container-insights-customers .text-field-widget input[type=checkbox] { float: left; width: 25px; }
.widget-container-insights-customers .text-field-widget label { float: right; width: 185px; position: relative; top: -6px; }

</style>

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=admin&c=user') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1>Gebruiker toevoegen</h1>
    <?php else : ?>
    <h1>Gebruiker bewerken</h1>
    <?php endif; ?>
</div>


<?php print $userForm->render() ?>



<script>

$('[name=user_type]').change(function() {
	toggleCustomerSelection();
});

function toggleCustomerSelection() {
	var c = $('.widget-container-insights-customers');

	if ($('[name=user_type]').val() == 'admin') {
		c.hide();
	} else {
		c.show();
	}
}

toggleCustomerSelection();

</script>



