
<div class="calendar-item-container">

    <div class="page-header">
    	<div class="toolbox">
    		<a href="javascript:void(0);" class="fa fa-chevron-circle-left popup-close-link"></a>
    		<?php if ($isNew == false) : ?>
    		<a href="javascript:void(0);" class="fa fa-trash delete-calendar-item"></a>
    		<?php endif;?>
    		<a href="javascript:void(0);" class="fa fa-save submit-calendar-item"></a>
    	</div>
    	
    	<h1>Agendapunt <?= $isNew ? 'toevoegen' : 'bewerken'?></h1>
    </div>
    
    
    <div class="error-container"></div>
    
    
	<?= $form->render() ?>    

</div>
