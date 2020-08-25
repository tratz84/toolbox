
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
			<th><?= t('File') ?></th>
			<th></th>
		</tr>
	</thead>
	
	<tbody>
		<?php for($x=0; $x < $filetemplates->count(); $x++) : ?>
			<?php $ft = $filetemplates->get($x) ?>
			<tr class="filetemplate-record" data-id="<?= esc_attr($ft->getId()) ?>">
				<td><?= esc_html($ft->getName()) ?></td>
				<td><?= esc_html($ft->getDescription()) ?></td>
				<td>
					<?= esc_html($ft->getFile()) ?>
				</td>
				<td>
					<input type="button" class="linkDoc" value="<?= t('Link file') ?>" />
					<?php if ($ft->getFile()) : ?>
					<input type="button" class="unlinkDoc" value="<?= t('Unlink file') ?>" />
					<?php endif; ?>
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



<script>

$('.filetemplate-record .linkDoc').click(function() {
	select_store_file(function(rec) {
		var id = $(this).closest('tr').data('id');

		linkTemplateToFile( id, rec.store_file_id );
	}.bind(this));
});

$('.filetemplate-record .unlinkDoc').click(function() {
	var id = $(this).closest('tr').data('id');
	
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=filesync&c=filetemplates'),
		data: {
			a: 'unlink_template',
			template_id: id
		},
		success: function(data, xhr, textStatus) {
			window.location = appUrl('/?m=filesync&c=filetemplates');
		}
	});
});


function linkTemplateToFile( template_id, storeFileId ) {
	$.ajax({
		type: 'POST',
		url: appUrl('/?m=filesync&c=filetemplates'),
		data: {
			a: 'link_template_to_file',
			template_id: template_id,
			store_file_id: storeFileId
		},
		success: function(data, xhr, textStatus) {
			window.location = appUrl('/?m=filesync&c=filetemplates');
		}
	});
}

</script>



