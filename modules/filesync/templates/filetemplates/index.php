
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
			<tr class="filetemplate-record" data-id="<?= esc_attr($ft->getId()) ?>">
				<td><?= esc_html($ft->getName()) ?></td>
				<td><?= esc_html($ft->getDescription()) ?></td>
				<td>
					<input type="button" class="linkDoc" value="Document koppelen" />
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
		console.log( rec );
		
		var id = $(this).closest('tr').data('id');
		
	}.bind(this));
	
});

function linkTemplateToFile( template_id, storeFileId ) {
	
	
}

</script>



