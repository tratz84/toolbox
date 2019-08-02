

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=template&a=add') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1><?= t('Overview templates') ?></h1>
</div>



<table class="list-response-table">
	<thead>
		<tr>
			<th><?= t('Template name') ?></th>
			<th><?= t('Path') ?></th>
		</tr>
	</thead>
	
	<tbody>
    	<?php foreach($templates as $templateName => $data) : ?>
    	<tr>
    		<td><?= esc_html($templateName) ?></td>
    		<td>
    			<?= esc_html($data['path']) ?>
    		</td>
    	</tr>
    	<?php endforeach; ?>
    	<?php if (count($templates) == 0) : ?>
    	<tr>
    		<td colspan="2" style="font-style: italic; text-align: center;"><?= t('No templates available') ?></td>
    	</tr>
    	<?php endif; ?>
	</tbody>

</table>