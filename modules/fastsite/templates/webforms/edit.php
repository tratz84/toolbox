
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


<div class="webform-fields form-generator" style="width: calc(100% - 320px); float: left;"></div>

<div class="clear" style="height: 100px;"></div>

<script>

var fieldTypes = <?= json_encode($fieldTypes) ?>;


$(document).ready(function() {
	$('.webform-fields').sortable({
		handle: '.move-handle'
	});
});

function add_webform_field(t) {
	$.ajax({
		url: appUrl('/?m=fastsite&c=webforms'),
		type: 'POST',
		data: {
			'a': 'load_widget',
			'class': t
		},
		success: function(data, xhr, textStatus) {
			var d = $( data.html );

			$('.webform-fields').append(d);
		},
		error: function() {
		}
	});
}


function add_keyval_option(obj) {
	var container = $(obj).closest('.webform-field');
	var optionsContainer = $(container).find('.widget-options');


	var ik = $('<input type="text" name="" placeholder="Key" />');
	var iv = $('<input type="text" name="" placeholder="Value" />');

	var r = $('<a class="fa fa-remove" href="javascript:void(0);" onclick="remove_keyval_option(this);"></a>');

	var c = $('<div class="option-container" />');

	c.append('<span class="handler option-move-handler fa fa-arrows-v" /> ');
	
	var opt1 = $('<span class="key" />');
	opt1.append( ik );

	var opt2 = $('<span class="val" />');
	opt2.append( iv );

	c.append(opt1);
	c.append(opt2);
	c.append(r);

	optionsContainer.append( c );

	$(optionsContainer).sortable({
		handle: '.option-move-handler'
	});
	
	
}

function remove_keyval_option(obj) {
	$(obj).closest('.option-container').remove();
}



</script>
