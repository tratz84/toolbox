
<div class="calendar-item-container">

    <div class="page-header">
    	<div class="toolbox">
    		<?php if ($readonly) : ?>
	    		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
    		<?php else : ?>
        		<a href="javascript:void(0);" class="fa fa-chevron-circle-left popup-close-link"></a>
        		<?php if ($isNew == false) : ?>
        		<a href="javascript:void(0);" class="fa fa-trash delete-calendar-item"></a>
        		<?php endif;?>
        		<a href="javascript:void(0);" class="fa fa-save submit-calendar-item"></a>
    		<?php endif; ?>
    	</div>
    	
    	<h1>
    		<?php if ($readonly) : ?>
    			<?= t('View calendaritem') ?>
    		<?php elseif ($isNew) : ?>
    			<?= t('Add calendaritem') ?>
    		<?php else : ?>
    			<?= t('Edit calendaritem') ?>
    		<?php endif; ?>
    	</h1>
    </div>
    
    
    <div class="error-container"></div>
    
    <?php if ($readonly) : ?>
    	<?= $form->renderReadonly() ?>
    <?php else : ?>
		<?= $form->render() ?>
	<?php endif; ?>    

</div>

<?php if ($readonly) : ?>
<script>
var t = $('.calendar-item-container .widget-locatie .widget-value').text();
t = $.trim(t);
if (t != '') {
	anchorMaps = $('<a class="fa fa-map-marker" target="_blank" style="margin-left: 5px;"></a>');
	anchorMaps.attr('href', 'https://www.google.com/maps/search/?api=1&query=' + encodeURI(t));
	$('.calendar-item-container .widget-locatie .widget-value').after( anchorMaps );
}

</script>
<?php endif; ?>


<?php if (!$readonly) : ?>
<script>

var updateItemActionWidget = function() {
    if ($('form.form-calendar-item-form [name=recurrence_type]').val() == '') {
    	$('.widget-item-action').show();
    } else {
    	$('.widget-item-action').hide();
    }
};
$('form.form-calendar-item-form [name=recurrence_type]').change( updateItemActionWidget );
updateItemActionWidget();

</script>
<?php endif; ?>

