

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=filesync&c=store') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Store statistics</h1>
</div>


<table>
	<tr>
		<th>Store name</th>
		<td><?= esc_html($store->getStoreName()) ?></td>
	</tr>
	<tr>
		<th>Totale store grootte</th>
		<td>
			<?= format_filesize($statisticsStore['size_all_files']) ?>
		</td>
	</tr>
	
	<tr>
		<th>Grootte actieve bestanden</th>
		<td>
			<?= format_filesize($statisticsStore['size_active_files']) ?>
		</td>
	</tr>

</table>

