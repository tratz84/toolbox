
<div class="page-header">
	
	<div class="toolbox">
		<a href="<?= appUrl('/?m=codegen&c=menu') ?>" class="fa fa-chevron-circle-left"></a>
	</div>

	<h1>Forms</h1>
</div>


<table class="list-response-table">

	<thead>
		<tr>
			<th>Module</th>
			<th>File</th>
		</tr>
	</thead>
	
	<tbody>
		<?php foreach($forms as $f) : ?>
		<tr class="clickable" onclick="window.open( appUrl('/?m=codegen&c=formgenerator&fm=<?= $f['module'] ?>&ff=' + <?= esc_attr(json_encode(urlencode($f['file']))) ?>), '_self' );">
			<td><?= esc_html($f['module']) ?></td>
			<td><?= esc_html($f['file']) ?></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	

</table>
