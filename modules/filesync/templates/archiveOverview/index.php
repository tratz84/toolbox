
<?php if (count($filetemplates)) : ?>
<div class=" js-menu-container">
	<ul id="menu" class="menu">
		<li><div><?= t('New document') ?></div>
			<ul>
				<?php foreach($filetemplates as $ft) : ?>
				<li>
					<div><a href="javascript:void(0);" onclick="filesync_createDocument(<?= esc_json_attr($ft->getId()) ?>);"><?= esc_html($ft->getName()) ?></a></div>
				</li>
				<?php endforeach; ?>
			</ul>
		</li>
	</ul>
</div>
<?php endif; ?>

<div class="clear" style="height: 10px;"></div>

<table class="filesync-archive-overview list-widget">

	<thead>
		<tr>
			<th><?= t('Filename') ?></th>
			<th><?= t('Subject') ?></th>
			<th><?= t('Public') ?></th>
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
			<tr class="clickable" onclick="window.open('<?= apply_filter('edit-store-file-url', appUrl('/?m=filesync&c=storefile&a=download&inline=1&id='.$sf->getStoreFileId()), ['store_file_id' => $sf->getStoreFileId()]) ?>', '_blank');">
				<td><?= esc_html($sf->getPath()) ?></td>
				<td><?= esc_html($sf->getField('subject')) ?></td>
				<td><?= $sf->getField('public') ? t('Yes'):t('No') ?></td>
			 	<td class="td-filesize"><?= format_filesize($sf->getField('filesize')) ?></td>
				<td class="td-date"><?= format_date($sf->getField('document_date'), 'd-m-Y') ?></td>
			</tr>
			<?php endforeach; ?>
		<?php endif; ?>
	</tbody>

</table>


