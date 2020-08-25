
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('File templates') ?></h1>
</div>



<table class="list-response-table">

	<thead>
		<tr>
			<th><?= t('Template name') ?></th>
			<th><?= t('Description') ?></th>
			<th></th>
		</tr>
	</thead>
	
	<tbody>
		<?php for($x=0; $x < $filetemplates->count(); $x++) : ?>
			<?php $ft = $filetemplates->get($x) ?>
			<tr>
				<td><?= esc_html($ft->getName()) ?></td>
				<td><?= esc_html($ft->getDescription()) ?></td>
				<td>
					<input type="button" value="Document koppelen" />
					
				</td>
			</tr>
		<?php endfor; ?>
    	<tr class="no-results-found">
    		<td colspan="3" class="no-results-found">
    			<?= t('No templates found') ?>
    		</td>
    	</tr>
	</tbody>

</table>


