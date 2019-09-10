

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
			<th><?= t('Active') ?></th>
			<th></th>
		</tr>
	</thead>
	
	<tbody>
    	<?php foreach($templates as $templateName => $data) : ?>
    	<tr onclick="window.location=appUrl('/?m=fastsite&c=templateEditor&n='+$(this).data('template-name'));" class="clickable" data-template-name="<?= esc_attr($templateName) ?>">
    		<td><?= esc_html($templateName) ?></td>
    		<td>
    			<?= esc_html($data['path']) ?>
    		</td>
    		<td><?= $data['active'] ? t('Yes') : t('No') ?></td>
    		<td>
    			<a href="<?= appUrl('/?m=fastsite&c=template&a=delete&n='.urlencode($templateName)) ?>" class="fa fa-remove delete"></a>
    		</td>
    	</tr>
    	<?php endforeach; ?>
    	<?php if (count($templates) == 0) : ?>
    	<tr>
    		<td colspan="4" style="font-style: italic; text-align: center;"><?= t('No templates available') ?></td>
    	</tr>
    	<?php endif; ?>
	</tbody>

</table>

<script>

$(document).ready(function() {
	handle_deleteConfirmation();
});
	
</script>