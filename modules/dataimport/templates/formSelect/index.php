
<div class="page-header">

	<h1><?= t('Select data type') ?></h1>
</div>



<table class="list-response-table">

	<thead>
		<tr>
			<th><?= t('Description') ?></th>
			<th><?= t('Form name') ?></th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach( $difContainer->getList() as $dif ) : ?>
		<tr class="clickable" onclick="window.location=<?= esc_json_attr(appUrl('/?m=dataimport&c=sheetImport&uid='.$dif['uid'])) ?>;">
			<td><?= esc_html($dif['description']) ?></td>
			<td><?= esc_html($dif['formClass']) ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>


</table>

