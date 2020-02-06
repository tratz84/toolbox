

<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=payment&c=import') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Import sheet</h1>
</div>


<?php if (isset($error)) : ?>
<div class="error">
	Error: <?= esc_html($error) ?>
</div>
<?php else : ?>

	<?= $form->render() ?>

<?php endif; ?>



<?php include_component('payment', 'import/sheet', 'import_sample', array('sheet_file' => $tmpfile))?>


<script>

$(document).ready(function() {
	$('.table-container.import-fields').find('select').change(function() {
		load_import_sample();
		
	});
});

function load_import_sample() {
	var data = serialize2object('.table-container.import-fields');
	data['sheet_file'] = <?= json_encode($tmpfile) ?>;

	$.ajax({
		url: appUrl('/?m=payment&c=import/sheet&a=sample_data'),
		data: data,
		success: function(data, xhr, textStatus) {
			if (data && data.success) {
				for (x in data.sample) {
					console.log(x);
					var inp = $('.table-container.import-fields').find('[name=example_' + x + ']');

					inp.closest('div.widget').find('span').text( data.sample[x] );
				}
			}
		}
	});
	
}


</script>

