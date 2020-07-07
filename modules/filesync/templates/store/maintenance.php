

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=store') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Store statistics</h1>
</div>


<table>
	<tr>
		<th><?= t('Store name') ?></th>
		<td><?= esc_html($store->getStoreName()) ?></td>
	</tr>
	<tr>
		<th><?= t('Total store size') ?></th>
		<td>
			<?= format_filesize($statisticsStore['size_all_files']) ?>
		</td>
	</tr>
	
	<tr>
		<th>
			<?= t('Size active files') ?>
			<?= infopopup(t('Share-stores may contain multiple versions of one file'))?>
		</th>
		<td>
			<?= format_filesize($statisticsStore['size_active_files']) ?>
		</td>
	</tr>

</table>

