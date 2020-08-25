
<div class="page-header">

	<div class="toolbox">
		<a href="javascript:void(0);" class="fa fa-times-circle popup-close-link"></a>
	</div>

	<h1><?= t('Template information') ?></h1>
</div>




<?php if (!$ft) : ?>
Requested template not found
<?php endif; ?>

<?php if ($ft) : ?>
	
	<div><b><?= t('Template name') ?>:</b> <?= $ft->getName() ?></div>
	<?php if ($ft->getDescription()) : ?>
	<div><b><?= t('Template description') ?>:</b> <?= $ft->getDescription() ?></div>
	<?php endif; ?>
	
	<br/>
	
	<table class="list-response-table">
		<thead>
			<tr>
				<th><?= t('Variable name') ?></th>
				<th><?= t('Description') ?></th>
				<th><?= t('Example') ?></th>
			</tr>
		</thead>
    	<tbody>
        	<?php foreach($ft->getVars() as $k => $props) : ?>
        	<tr>
        		<td>[[<?= $k ?>]]</td>
        		<td><?= esc_html($props['description']) ?></td>
        		<td><?= esc_html($props['exampleValue']) ?></td>
        	</tr>
        	<?php endforeach; ?>
        </tbody>
	</table>

<?php endif; ?>
