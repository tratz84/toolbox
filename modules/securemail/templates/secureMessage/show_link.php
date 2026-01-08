
<div class="page-header">
	<div class="toolbox">
		<?php if (isset($sm)) : ?>
			<a href="<?= appUrl('/?m=securemail&c=secureMessage&a=edit&sm_id='.$sm->getSecureMessageId()) ?>" class="fa fa-chevron-circle-left"></a>
		<?php else : ?>
			<a href="<?= appUrl('/?m=securemail&c=secureMessage') ?>" class="fa fa-chevron-circle-left"></a>
		<?php endif; ?>
	</div>
	
	<h1><?= t('Link to secret') ?></h1>
</div>


<div style="font-style: italic; margin-top: 25px;">
    <?php if ($sm) : ?>
    	Link naar bericht
    	<br/>
    	
    	<div>
    		<input type="text" name="public_link" value="<?= esc_attr($sm->getPublicLink()) ?>" style="width: 500px" readonly=readonly />
    		
    		<a href="javascript:void(0);" onclick="copyToClipboard( 'input[name=public_link]' );" class="fa fa-copy textfield-copy"></a>
    	</div>
    	
    	<br/><br/>
    	
    	<a href="<?= appUrl('/?m=securemail&c=secureMessage&a=edit&sm_id='.$sm->getSecureMessageId()) ?>">&lt; Terug</a>
    	
    <?php else : ?>
    	<?php if (isset($error)) : ?>
    		<?= $error ?>
    	<?php else : ?>
    		Link niet gevonden
    	<?php endif; ?>
    <?php endif; ?>
</div>