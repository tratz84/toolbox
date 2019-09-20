
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=webforms') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

	<?php if ($isNew) : ?>
	<h1><?= t('Add form') ?></h1>
	<?php else : ?>
	<h1><?= t('Edit form') ?></h1>
	<?php endif; ?>
</div>


<?= $form->render() ?>


<div class="" style="float: right; width: 300px;">
    <?php foreach($fieldTypes as $ft) : ?>
    	<div>
    		<a href="javascript:void(0);" onclick="<?= esc_attr('add_webform_field('.json_encode($ft['class']).');') ?>">Add <?= esc_html($ft['label']) ?> widget</a>
    	</div>
    <?php endforeach; ?>
</div>

<script>

var fieldTypes = <?= json_encode($fieldTypes) ?>;

function add_webform_field(t) {
// 	.widget-container-webform-fields

	$.ajax({
		url: appUrl('/?m=fastsite&c=webforms'),
		data: {
			'a': 'load_widget',
			'class': t
		},
		success: function(data, xhr, textStatus) {
			var d = $('<div />');
			d.html( data );

			$('.widget-container-webform-fields').append(d);
		},
		error: function() {
		}
	});
	
}



</script>
