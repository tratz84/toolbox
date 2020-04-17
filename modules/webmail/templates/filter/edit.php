

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=webmail&c=filter') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="javascript:void(0);" class="fa fa-save submit-form"></a>
	</div>

    <?php if ($isNew) : ?>
    <h1>Filter toevoegen</h1>
    <?php else : ?>
    <h1>Filterbewerken</h1>
    <?php endif; ?>
</div>

<?php print $form->render() ?>



<br/>


<script>

$(document).ready(function() {
    var lefw = $('.webmail-form-list-filter-condition-widget').get(0).lefw;
    lefw.setCallbackAddRecord(function(row) {
    	selectFilterType_Change( row );

    	$(row).find('.input-filter-type select').change(function() {
        	selectFilterType_Change( $(this).closest('tr') );
    	});
    });
    $('.input-filter-type select').each(function() {
    	selectFilterType_Change( $(this).closest('tr') );
    });
    $('.input-filter-type select').change(function() {
    	selectFilterType_Change( $(this).closest('tr') );
    });

    
    var lefw2 = $('.webmail-form-list-filter-action-widget').get(0).lefw;
    lefw2.setCallbackAddRecord(function(row) {
    	selectFilterAction_Change( row );
        $(row).find('.widget-filter-action select').change(function(index, node) {
            selectFilterAction_Change( $(this).closest('tr') );
        });
    });
    
    $('.widget-filter-action select').change(function(index, node) {
        selectFilterAction_Change( $(this).closest('tr') );
    });
    $('.widget-filter-action select').each(function(index, node) {
        selectFilterAction_Change( $(this).closest('tr') );
    });
});



function selectFilterType_Change(tr) {
	if ($(tr).find('.input-filter-type').length == 0)
		return;
	
	var v = $(tr).find('.input-filter-type select').val();
	
	if (v == 'is_spam') {
		$(tr).find('.input-filter-field').css('visibility', 'hidden');
		$(tr).find('.input-filter-pattern').css('visibility', 'hidden');
	} else {
		$(tr).find('.input-filter-field').css('visibility', 'visible');
		$(tr).find('.input-filter-pattern').css('visibility', 'visible');
	}
}

function selectFilterAction_Change(tr) {
	if ($(tr).find('.input-filter-action').length == 0)
		return;
	
	var v = $(tr).find('.widget-filter-action select').val();
	
	if (v == 'move_to_folder') {
		$(tr).find('.widget-move-to-folder-filter-action-value').show();
		$(tr).find('.widget-set-action-filter-action-value').hide();
	} else if (v == 'set_action') {
		$(tr).find('.widget-move-to-folder-filter-action-value').hide();
		$(tr).find('.widget-set-action-filter-action-value').show();
	}
	
}




</script>

