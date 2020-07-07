

<table class="filesync-archive-overview list-widget">

	<thead>
		<tr>
			<th><?= t('Filename') ?></th>
			<th><?= t('Subject') ?></th>
			<th class="th-filesize"><?= t('File size') ?></th>
			<th class="th-date"><?= t('Date') ?></th>
		</tr>
	</thead>
	
	<tbody>
		<?php if (count($storeFiles) == 0) : ?>
		<tr>
			<td colspan="4" style="text-align: center; font-style: italic;">
				<?= t('No results found') ?>
			</td>
		</tr>
		<?php else : ?>
			<?php foreach($storeFiles as $sf) : ?>
			<tr class="clickable" onclick="window.open('<?= appUrl('/?m=filesync&c=storefile&a=download&inline=1&id='.$sf->getStoreFileId()) ?>', '_blank');">
				<td><?= esc_html($sf->getPath()) ?></td>
				<td><?= esc_html($sf->getField('subject')) ?></td>
			 	<td class="td-filesize"><?= format_filesize($sf->getField('filesize')) ?></td>
				<td class="td-date"><?= format_date($sf->getField('document_date'), 'd-m-Y') ?></td>
			</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>

</table>


