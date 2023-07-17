
<div class="page-header">
	<div class="toolbox">
		<a href="<?= appurl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
		<a href="<?= appUrl('/?m=webmail&c=externalTokens&a=add') ?>" class="fa fa-plus"></a>
	</div>
	
	<h1><?= t('External tokens') ?></h1>
</div>


<table class="list-response-table">

	<thead>
		<tr>
			<th>Id</th>
			<th>Description</th>
			<th>
			</th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach( $azureTokens as $t) : ?>
		<tr>
			<td><?= $t->getWebmailAzureTokenId() ?></td>
			<td><?= esc_html($t->getDescription()) ?></td>
			<td>
				
			</td>
		</tr>
		<?php endforeach; ?>
		<?php if (count($azureTokens) == 0) : ?>
		<tr>
			<td colspan="3" class="no-results-found">
				<?= t('No results found') ?>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>

</table>

