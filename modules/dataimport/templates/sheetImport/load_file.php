
<div class="page-header">
	<div class="toolbox">
		<a href="" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('Import data') ?>: <?= esc_html($dif['description']) ?></h1>
</div>


<form method="post">

	<?= $di->render() ?>

	<br/>
	
	<?php if ( $di->countRowsWithErrors() > 0 ) : ?>
	<div class="errors">
		<?= $di->countRowsWithErrors() ?> record(s) with errors
	</div>
	<?php endif; ?>
	
	<input type="submit" name="validate" value="<?= t('Validate') ?>" />
	
	<?php if ($allowImport) : ?>
	<input type="submit" name="import" value="<?= t('Import') ?>" />
	<?php endif; ?>

</form>

<br/><br/>



<script>

$(document).ready(function() {
	$('[name=import_row_all]').click(function() {
		if ($(this).prop('checked')) {
			$('.import-row').prop('checked', true);
		}
		else {
			$('.import-row').prop('checked', false);
		}
	});

	$('.import-row').click(function() {
		var count_all = $('.import-row').length;
		var count_checked = $('.import-row:checked').length;

		if (count_all == count_checked) {
			$('[name=import_row_all]').prop('checked', true);
		}
		else {
			$('[name=import_row_all]').prop('checked', false);
		}
	});
	
});


</script>

