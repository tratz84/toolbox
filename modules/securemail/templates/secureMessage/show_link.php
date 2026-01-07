
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=securemail&c=secureMessage') ?>" class="fa fa-chevron-circle-left"></a>
	</div>
	
	<h1><?= t('Link to secret') ?></h1>
</div>


<div style="font-style: italic; margin-top: 25px;">
    <?php if ($sm) : ?>
    	Link naar bericht: <a href="<?= $sm->getPublicLink() ?>"><?= esc_html($sm->getPublicLink()) ?></a>
    <?php else : ?>
    	<?php if (isset($error)) : ?>
    		<?= $error ?>
    	<?php else : ?>
    		Link niet gevonden
    	<?php endif; ?>
    <?php endif; ?>
</div>