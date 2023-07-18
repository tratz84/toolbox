
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
			<th style="width: 50px;">Id</th>
			<th>Description</th>
			<th style="width: 150px;">Status</th>
			<th>
			</th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach( $azureTokens as $t) : ?>
		<tr class="clickable" onclick="window.location = '<?= appUrl('/?m=webmail&c=externalTokens&a=edit_azure&id='.$t->getWebmailAzureTokenId()) ?>';">
			<td><?= $t->getWebmailAzureTokenId() ?></td>
			<td><?= esc_html($t->getDescription()) ?></td>
			<td><?= $t->getConnectionStatus() ?></td>
			<td>
				
			</td>
		</tr>
		<?php endforeach; ?>
		<?php if (count($azureTokens) == 0) : ?>
		<tr>
			<td colspan="4" class="no-results-found">
				<?= t('No results found') ?>
			</td>
		</tr>
		<?php endif; ?>
	</tbody>

</table>

