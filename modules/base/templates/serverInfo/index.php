

<div class="page-header">
	<div class="toolbox">
		<a href="<?= appUrl('/?m=base&c=masterdata/index') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1><?= t('Server info') ?></h1>
</div>



<table class="list-response-table">
	<thead>
		<tr>
			<th>Description</th>
			<th>Value</th>
			<th>Error</th>
		</tr>
	</thead>

	<tbody>
	<?php foreach($sic->getInfo() as $i) : ?>
	<tr>
		<td style="width: 50%;"><?= $i['description'] ?></td>
		<td style="<?= $i['error'] ? 'color: #f00;' : '' ?>"><?= esc_html($i['value']) ?></td>
		<td style="width: 30%;"><?= esc_html($i['error']) ?></td>
	</tr>
	<?php endforeach; ?>
	</tbody>

</table>



