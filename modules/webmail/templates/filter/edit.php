

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
    var lefw = $('.webmailform-list-filter-condition-widget').get(0).lefw;
    console.log( lefw );

    lefw.setCallbackAddRecord(function(row) {
    	selectFilterType_Change( row );
    });

    $('.input-filter-type select').change(function() {
    	var tr = $(this).closest('tr');
    	selectFilterType_Change( tr );
    });

    $('.input-filter-type select').each(function(index, node) {
        selectFilterType_Change( $(node).closest('tr') );
    });
});



function selectFilterType_Change(tr) {
	var v = $(tr).find('.input-filter-type select').val();
	console.log(v);
	if (v == 'is_spam') {
		$(tr).find('.input-filter-field').css('visibility', 'hidden');
		$(tr).find('.input-filter-pattern').css('visibility', 'hidden');
	} else {
		$(tr).find('.input-filter-field').css('visibility', 'visible');
		$(tr).find('.input-filter-pattern').css('visibility', 'visible');
	}
}




</script>

