

<div class="page-header">
	<div class="toolbox list-toolbox">
		<a href="<?= appUrl('/?m=fastsite&c=template') ?>" class="fa fa-chevron-circle-left"></a>
	</div>
	
    <h1><?= t('Editing template ') ?> </h1>
</div>


<table>

	<thead>
		<tr>
			<th><?= t('File') ?></th>
			<th></th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach($files as $f) : ?>
		<tr>
			<td><?= esc_html( $f ) ?></td>
			<td>
				<a href="<?= appUrl('/?m=fastsite&c=templateEditor&a=edit&n='.urlencode($templateName).'&f='.urlencode($f)) ?>" class="fa fa-edit"></a>
				<a href="<?= appUrl('/?m=fastsite&c=templateEditor&a=delete&n='.urlencode($templateName).'&f='.urlencode($f)) ?>" class="fa fa-remove"></a>
			</td>
		</tr>
		<?php endforeach; ?>
	</tbody>

</table>
