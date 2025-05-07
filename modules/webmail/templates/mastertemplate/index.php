
<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=webmail&c=template') ?>" class="fa fa-chevron-circle-left"></a>
		
		<a href="<?= appUrl('/?m=webmail&c=mastertemplate&a=edit') ?>" class="fa fa-plus"></a>
	</div>
	
    <h1><?= t('Master Templates') ?></h1>
</div>



<table class="list-response-table">
	<thead>
		<tr>
			<th><?= t('Name') ?></th>
			<th><?= t('Last modified') ?></th>
			<th></th>
		</tr>
	</thead>
	
	
	<tbody>
		<?php if (count($mts) == 0) : ?>
		<tr>
			<td colspan="100%" class="no-results-found">
				<?= t('No results found') ?>
			</td>
		</tr>
		<?php else : ?>
			<?php foreach($mts as $mt) : ?>
			<tr class="clickable" onclick="row_Click(this);" data-id="<?= $mt->getMasterTemplateId() ?>">
				<td><?= esc_html($mt->getName()) ?></td>
				<td style="width: 150px;">
					<?= format_date($mt->getEdited(), 'd-m-Y H:i:s') ?>
				</td>
				<td class="td- actions" style="width: 100px;">
					<a href="<?= appUrl('/?m=webmail&c=mastertemplate&a=edit&id='.$mt->getMasterTemplateId()) ?>" class="fa fa-pencil"></a>
					
					<a href="<?= appUrl('/?m=webmail&c=mastertemplate&a=delete&id='.$mt->getMasterTemplateId()) ?>" class="fa fa-trash delete"></a>
				</td>
			</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>

</table>



<script>

handle_deleteConfirmation();



function row_Click( tr ) {
	let evt = window.event;

	if ($(evt.target).hasClass('.actions') || $(evt.target).closest('.actions').length > 0)
		return;
	
	let id = $(tr).data('id');
	window.location = appUrl( '/?m=webmail&c=mastertemplate&a=edit&id=' + id );
}


</script>

