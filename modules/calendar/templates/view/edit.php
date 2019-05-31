
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
